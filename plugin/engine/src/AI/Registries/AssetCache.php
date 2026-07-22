<?php

namespace AIAnalysisEngine\AI\Registries;

use AIAnalysisEngine\AI\DTO\Asset;

class AssetCache
{
    private AssetRegistry $registry;
    /** @var array<string, Asset> */
    private array $cache = [];

    public function __construct(AssetRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function get(string $type, string $id, string $version): Asset
    {
        $key = "{$type}::{$id}::{$version}";
        
        if (!isset($this->cache[$key])) {
            // Load and parse JSON into DTO only once
            $this->cache[$key] = $this->registry->get($type, $id, $version);
        }

        return $this->cache[$key];
    }
}
