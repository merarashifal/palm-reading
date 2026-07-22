<?php

namespace AIAnalysisEngine\AI\Contracts;

use AIAnalysisEngine\AI\DTO\ProviderRequest;
use AIAnalysisEngine\AI\DTO\ProviderResponse;

interface TransportInterface
{
    /**
     * Dispatches the HTTP request and returns the raw response.
     */
    public function send(ProviderRequest $request): ProviderResponse;
}
