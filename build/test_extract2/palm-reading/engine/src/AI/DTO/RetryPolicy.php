<?php

namespace AIAnalysisEngine\AI\DTO;

class RetryPolicy
{
    public int $maxRetries;
    public int $backoffMs;
    public int $timeoutMs;
    public array $retryOnStatusCodes;

    public function __construct(int $maxRetries = 3, int $backoffMs = 1000, int $timeoutMs = 30000, array $retryOnStatusCodes = [429, 500, 502, 503, 504])
    {
        $this->maxRetries = $maxRetries;
        $this->backoffMs = $backoffMs;
        $this->timeoutMs = $timeoutMs;
        $this->retryOnStatusCodes = $retryOnStatusCodes;
    }
}
