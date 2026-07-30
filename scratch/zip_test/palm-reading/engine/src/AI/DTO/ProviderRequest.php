<?php

namespace AIAnalysisEngine\AI\DTO;

class ProviderRequest
{
    public readonly string $url;
    public readonly string $method;
    public readonly array $headers;
    public readonly string $body;
    public readonly int $timeoutMs;
    public readonly RetryPolicy $retryPolicy;

    public function __construct(
        string $url,
        string $method = 'POST',
        array $headers = [],
        string $body = '',
        int $timeoutMs = 30000,
        ?RetryPolicy $retryPolicy = null
    ) {
        $this->url = $url;
        $this->method = $method;
        $this->headers = $headers;
        $this->body = $body;
        $this->timeoutMs = $timeoutMs;
        $this->retryPolicy = $retryPolicy ?? new RetryPolicy();
    }
}
