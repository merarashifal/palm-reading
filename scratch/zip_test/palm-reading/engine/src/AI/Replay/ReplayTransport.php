<?php

namespace AIAnalysisEngine\AI\Replay;

use AIAnalysisEngine\AI\Contracts\TransportInterface;
use AIAnalysisEngine\AI\DTO\ProviderRequest;
use AIAnalysisEngine\AI\DTO\ProviderResponse;

class ReplayTransport implements TransportInterface
{
    private ProviderResponse $cannedResponse;

    public function __construct(ProviderResponse $cannedResponse)
    {
        $this->cannedResponse = $cannedResponse;
    }

    public function send(ProviderRequest $request): ProviderResponse
    {
        // Simply returns the canned response for debugging/testing
        return $this->cannedResponse;
    }
}
