<?php

namespace AIAnalysisEngine\AI\Providers\DTO;

class ExtractedFeature
{
    private string $id;
    private string $category;
    private string $type;
    private string $status;
    private float $confidence;
    private array $geometry;
    private array $evidence;
    private array $attributes;

    public function __construct(
        string $id,
        string $category,
        string $type,
        string $status,
        float $confidence,
        array $geometry,
        array $evidence,
        array $attributes = []
    ) {
        $this->id = $id;
        $this->category = $category;
        $this->type = $type;
        $this->status = $status;
        $this->confidence = $confidence;
        $this->geometry = $geometry;
        $this->evidence = $evidence;
        $this->attributes = $attributes;
    }

    public function getId(): string { return $this->id; }
    public function getCategory(): string { return $this->category; }
    public function getType(): string { return $this->type; }
    public function getStatus(): string { return $this->status; }
    public function getConfidence(): float { return $this->confidence; }
    public function getGeometry(): array { return $this->geometry; }
    public function getEvidence(): array { return $this->evidence; }
    public function getAttributes(): array { return $this->attributes; }
}
