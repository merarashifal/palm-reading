<?php

namespace AIAnalysisEngine\AI\DTO;

class NormalizedFeature
{
    public string $id;
    public string $analysis;
    public string $feature;
    public string $value;
    public float $confidence;
    public int $weight;
    public string $source;
    public string $provider;
    public string $providerVersion;
    public ?BoundingRegion $coordinates;
    public array $metadata;

    public function __construct(
        string $id,
        string $analysis,
        string $feature,
        string $value,
        float $confidence,
        int $weight,
        string $source,
        string $provider,
        string $providerVersion,
        ?BoundingRegion $coordinates = null,
        array $metadata = []
    ) {
        $this->id = $id;
        $this->analysis = $analysis;
        $this->feature = $feature;
        $this->value = $value;
        $this->confidence = $confidence;
        $this->weight = $weight;
        $this->source = $source;
        $this->provider = $provider;
        $this->providerVersion = $providerVersion;
        $this->coordinates = $coordinates;
        $this->metadata = $metadata;
    }
}
