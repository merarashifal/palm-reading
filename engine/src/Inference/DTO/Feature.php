<?php

namespace AIAnalysisEngine\Inference\DTO;

class Feature
{
    public string $id;
    public string $analysis;
    public string $feature;
    public string $value;
    public float $confidence;
    public string $source;
    public array $metadata = [];

    public function __construct(
        string $id,
        string $analysis,
        string $feature,
        string $value,
        float $confidence = 1.0,
        string $source = 'vision',
        array $metadata = []
    ) {
        $this->id = $id;
        $this->analysis = $analysis;
        $this->feature = $feature;
        $this->value = $value;
        $this->confidence = $confidence;
        $this->source = $source;
        $this->metadata = $metadata;
    }
}
