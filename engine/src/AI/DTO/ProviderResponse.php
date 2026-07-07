<?php

namespace AIAnalysisEngine\AI\DTO;

class ProviderResponse
{
    public int $statusCode;
    public array $headers;
    public string $body;
    public string $receivedAt;

    public ?string $rawResponse = null;
    public array $parsedResponse = [];

    public function __construct(int $statusCode, array $headers, string $body)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
        $this->receivedAt = date('c');
    }
}
