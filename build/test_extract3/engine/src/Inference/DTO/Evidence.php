<?php

namespace AIAnalysisEngine\Inference\DTO;

class Evidence
{
    public string $id;
    public string $feature;
    public string $value;
    public float $aiConfidence;
    public int $weight;
    public bool $isDerived;

    public function __construct(
        string $id,
        string $feature,
        string $value,
        float $aiConfidence,
        int $weight = 100,
        bool $isDerived = false
    ) {
        $this->id = $id;
        $this->feature = $feature;
        $this->value = $value;
        $this->aiConfidence = $aiConfidence;
        $this->weight = $weight;
        $this->isDerived = $isDerived;
    }
}
