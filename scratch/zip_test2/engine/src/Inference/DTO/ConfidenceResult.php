<?php

namespace AIAnalysisEngine\Inference\DTO;

class ConfidenceResult
{
    public float $ai;
    public float $inference;
    public float $relationship;
    public float $final;

    public function __construct(float $ai = 0.0, float $inference = 0.0, float $relationship = 0.0)
    {
        $this->ai = $ai;
        $this->inference = $inference;
        $this->relationship = $relationship;
        $this->calculateFinal();
    }

    public function calculateFinal(): void
    {
        // Simple placeholder aggregation. Will be complex logic later.
        $this->final = min(1.0, max(0.0, ($this->ai + $this->inference + $this->relationship) / 3));
    }
}
