<?php

namespace LumenCommandCenter\Core;

use InvalidArgumentException;
use RuntimeException;

/**
 * DashboardRegistry manages the lifecycle and registration of SaaS dashboard components.
 */
class DashboardRegistry
{
    private array $components = [];
    private bool $isLocked = false;

    public function register(string $id, array $config): void
    {
        if ($this->isLocked) {
            throw new RuntimeException("Registry is locked. Cannot register component: {$id}");
        }

        if (!isset($config['component'], $config['props'])) {
            throw new InvalidArgumentException("Component configuration must contain 'component' and 'props' keys.");
        }

        $this->components[$id] = array_merge(['registered_at' => microtime(true)], $config);
    }

    public function resolve(string $id): array
    {
        if (!isset($this->components[$id])) {
            throw new InvalidArgumentException("Component with ID '{$id}' not found in registry.");
        }

        return $this->components[$id];
    }

    public function lock(): void
    {
        $this->isLocked = true;
    }

    public function getManifest(): array
    {
        return $this->components;
    }
}