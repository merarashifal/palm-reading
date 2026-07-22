<?php

namespace AIAnalysisEngine\Inference\DTO;

class Recommendation
{
    public string $product;
    public int $priority;
    public string $reason;
    public string $visibility;

    public function __construct(string $product, int $priority, string $reason, string $visibility)
    {
        $this->product = $product;
        $this->priority = $priority;
        $this->reason = $reason;
        $this->visibility = $visibility;
    }
}
