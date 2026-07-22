<?php

namespace AIAnalysisEngine\AI\Providers\DTO;

class ExtractedFeatureCollection
{
    private string $schemaVersion;
    private ?string $hand;
    private ?array $source;
    private ?array $imageQuality;
    
    /** @var ExtractedFeature[] */
    private array $features = [];

    public function __construct(array $payload)
    {
        $this->schemaVersion = $payload['schema_version'] ?? 'unknown';
        $this->hand = $payload['hand'] ?? null;
        $this->source = $payload['source'] ?? null;
        $this->imageQuality = $payload['image_quality'] ?? null;

        $featuresRaw = $payload['features'] ?? [];
        foreach ($featuresRaw as $feat) {
            $this->features[] = new ExtractedFeature(
                $feat['feature_id'] ?? uniqid('feat_'),
                $feat['feature_revision'] ?? 1,
                $feat['category_id'] ?? null,
                $feat['category'] ?? 'unknown',
                $feat['type'] ?? 'unknown',
                $feat['status'] ?? 'uncertain',
                (float)($feat['visual_confidence'] ?? 0.0),
                (float)($feat['geometry_confidence'] ?? 0.0),
                $feat['geometry'] ?? ['type' => 'point', 'coordinates' => []],
                $feat['bbox'] ?? [],
                $feat['evidence'] ?? [],
                $feat['attributes'] ?? []
            );
        }
    }

    public function getSchemaVersion(): string { return $this->schemaVersion; }
    public function getHand(): ?string { return $this->hand; }
    public function getSource(): ?array { return $this->source; }
    public function getImageQuality(): ?array { return $this->imageQuality; }
    
    /**
     * @return ExtractedFeature[]
     */
    public function getFeatures(): array { return $this->features; }
}
