<?php

namespace AIAnalysisEngine\AI\DTO;

class ProviderExecution
{
    public int $queueTimeMs = 0;
    public int $requestTimeMs = 0;
    public int $processingTimeMs = 0;
    public int $responseSizeBytes = 0;
    public int $retryCount = 0;
    public bool $cacheHit = false;

    public int $latencyMs = 0;
    public ?string $requestId = null;
    public array $warnings = [];
    public ?string $finishReason = null;
    public CostBreakdown $cost;

    public function __construct()
    {
        $this->cost = new CostBreakdown();
    }
}
