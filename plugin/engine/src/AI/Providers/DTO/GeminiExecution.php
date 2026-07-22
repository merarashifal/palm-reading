<?php

namespace AIAnalysisEngine\AI\Providers\DTO;

class GeminiExecution
{
    public float $startedAt;
    public float $finishedAt;
    public float $durationMs;
    public int $httpCode;
    public int $promptTokens;
    public int $completionTokens;
    public float $estimatedCostInr;
    public int $retryCount;

    public function __construct(
        float $startedAt,
        float $finishedAt,
        float $durationMs,
        int $httpCode,
        int $promptTokens,
        int $completionTokens,
        float $estimatedCostInr,
        int $retryCount
    ) {
        $this->startedAt = $startedAt;
        $this->finishedAt = $finishedAt;
        $this->durationMs = $durationMs;
        $this->httpCode = $httpCode;
        $this->promptTokens = $promptTokens;
        $this->completionTokens = $completionTokens;
        $this->estimatedCostInr = $estimatedCostInr;
        $this->retryCount = $retryCount;
    }

    public function toArray(): array
    {
        return [
            'started_at' => $this->startedAt,
            'finished_at' => $this->finishedAt,
            'duration_ms' => $this->durationMs,
            'http_code' => $this->httpCode,
            'prompt_tokens' => $this->promptTokens,
            'completion_tokens' => $this->completionTokens,
            'estimated_cost_inr' => $this->estimatedCostInr,
            'retry_count' => $this->retryCount
        ];
    }
}
