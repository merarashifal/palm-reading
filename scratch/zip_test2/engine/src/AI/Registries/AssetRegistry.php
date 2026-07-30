<?php

namespace AIAnalysisEngine\AI\Registries;

use AIAnalysisEngine\AI\DTO\Asset;

class AssetRegistry
{
    /** @var array<string, Asset> */
    private array $assets = [];

    public function register(Asset $asset): void
    {
        $key = $this->buildKey($asset->type, $asset->id, $asset->version);
        $this->assets[$key] = $asset;
    }

    public function get(string $type, string $id, string $version): Asset
    {
        $key = $this->buildKey($type, $id, $version);
        if (!isset($this->assets[$key])) {
            throw new \RuntimeException("Asset not found: {$key}");
        }
        return $this->assets[$key];
    }

    private function buildKey(string $type, string $id, string $version): string
    {
        return "{$type}::{$id}::{$version}";
    }
}
