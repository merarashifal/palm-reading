<?php

namespace AIAnalysisEngine\AI\Factories;

use AIAnalysisEngine\AI\Contracts\TransportInterface;

class TransportFactory
{
    /** @var array<string, TransportInterface> */
    private array $transports = [];

    public function register(string $environment, TransportInterface $transport): void
    {
        $this->transports[$environment] = $transport;
    }

    public function create(string $environment = 'production'): TransportInterface
    {
        if (!isset($this->transports[$environment])) {
            throw new \RuntimeException("Transport not configured for environment: {$environment}");
        }
        return $this->transports[$environment];
    }
}
