<?php

namespace AIAnalysisEngine\AI\DTO;

class CostBreakdown
{
    public int $promptTokens = 0;
    public int $completionTokens = 0;
    public int $imageTokens = 0;
    public int $cacheTokens = 0;
    public float $totalCost = 0.0;
    public string $currency = 'USD';
}
