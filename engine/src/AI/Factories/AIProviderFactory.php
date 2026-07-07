<?php

namespace AIAnalysisEngine\AI\Factories;

use AIAnalysisEngine\AI\Contracts\AIAdapterInterface;
use AIAnalysisEngine\AI\Providers\ProviderRegistry;

class AIProviderFactory
{
    private ProviderRegistry $registry;

    public function __construct(ProviderRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function create(string $providerProfile): AIAdapterInterface
    {
        // e.g. "gemini-pro" mapping to the correct pipeline driver
        return $this->registry->driver($providerProfile);
    }
}
