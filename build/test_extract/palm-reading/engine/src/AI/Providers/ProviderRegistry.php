<?php

namespace AIAnalysisEngine\AI\Providers;

use AIAnalysisEngine\AI\Contracts\AIAdapterInterface;

class ProviderRegistry
{
    /** @var array<string, AIAdapterInterface> */
    private array $providers = [];

    public function register(string $name, AIAdapterInterface $provider): void
    {
        $this->providers[$name] = $provider;
    }

    public function driver(string $name): AIAdapterInterface
    {
        if (!isset($this->providers[$name])) {
            throw new \RuntimeException("Provider driver not registered: {$name}");
        }
        return $this->providers[$name];
    }
}
