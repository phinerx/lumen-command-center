<?php

namespace LumenCommandCenter\Core;

use InvalidArgumentException;
use RuntimeException;

/**
 * DashboardRegistry manages the lifecycle and registration of premium UI components.
 */
final class DashboardRegistry
{
    private static ?DashboardRegistry $instance = null;
    private array $components = [];
    private array $registrySchema = ['id', 'version', 'component_class'];

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function register(string $id, array $definition): void
    {
        foreach ($this->registrySchema as $key) {
            if (!isset($definition[$key])) {
                throw new InvalidArgumentException("Component definition missing required key: {$key}");
            }
        }

        if (isset($this->components[$id])) {
            throw new RuntimeException("Component with ID '{$id}' is already registered.");
        }

        $this->components[$id] = array_merge($definition, ['registered_at' => microtime(true)]);
    }

    public function resolve(string $id): array
    {
        if (!isset($this->components[$id])) {
            throw new RuntimeException("Component '{$id}' not found in registry.");
        }
        return $this->components[$id];
    }

    public function __clone() {}
    public function __wakeup() { throw new RuntimeException("Cannot unserialize singleton."); }
}