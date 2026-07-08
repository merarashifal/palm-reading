<?php

namespace AIAnalysisEngine\Inference\DTO;

class Insight
{
    public string $id;
    public string $title;
    public string $summary;
    public string $description;
    public float $confidence;
    public int $importance;
    public string $visibility; // e.g. "free", "premium"
    public string $category;
    public array $matchedRule; // The raw rule data that matched
    public array $evidence; // Evidence items
    public ?string $unlockReason;

    public function __construct(
        string $id,
        string $title,
        string $summary,
        string $description,
        float $confidence,
        int $importance,
        string $visibility,
        string $category,
        array $matchedRule,
        array $evidence,
        ?string $unlockReason = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->summary = $summary;
        $this->description = $description;
        $this->confidence = $confidence;
        $this->importance = $importance;
        $this->visibility = $visibility;
        $this->category = $category;
        $this->matchedRule = $matchedRule;
        $this->evidence = $evidence;
        $this->unlockReason = $unlockReason;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $this->summary,
            'description' => $this->description,
            'confidence' => $this->confidence,
            'importance' => $this->importance,
            'visibility' => $this->visibility,
            'category' => $this->category,
            'matchedRule' => $this->matchedRule,
            'evidence' => $this->evidence,
            'unlock_reason' => $this->unlockReason,
        ];
    }
}
