<?php

namespace AIAnalysisEngine\AI\Providers\DTO;

class ExtractedFeature
{
    private string $id;
    private int $revision;
    private ?string $categoryId;
    private string $category;
    private string $type;
    private string $status;
    private float $visualConfidence;
    private float $geometryConfidence;
    private array $geometry;
    private array $bbox;
    private array $evidence;
    private array $attributes;

    public function __construct(
        string $id,
        int $revision,
        ?string $categoryId,
        string $category,
        string $type,
        string $status,
        float $visualConfidence,
        float $geometryConfidence,
        array $geometry,
        array $bbox,
        array $evidence,
        array $attributes = []
    ) {
        $this->id = $id;
        $this->revision = $revision;
        $this->categoryId = $categoryId;
        $this->category = $category;
        $this->type = $type;
        $this->status = $status;
        $this->visualConfidence = $visualConfidence;
        $this->geometryConfidence = $geometryConfidence;
        $this->geometry = $geometry;
        $this->bbox = $bbox;
        $this->evidence = $evidence;
        $this->attributes = $attributes;
    }

    public function getId(): string { return $this->id; }
    public function getRevision(): int { return $this->revision; }
    public function getCategoryId(): ?string { return $this->categoryId; }
    public function getCategory(): string { return $this->category; }
    public function getType(): string { return $this->type; }
    public function getStatus(): string { return $this->status; }
    public function getVisualConfidence(): float { return $this->visualConfidence; }
    public function getGeometryConfidence(): float { return $this->geometryConfidence; }
    public function getGeometry(): array { return $this->geometry; }
    public function getBbox(): array { return $this->bbox; }
    public function getEvidence(): array { return $this->evidence; }
    public function getAttributes(): array { return $this->attributes; }
}
