<?php

namespace AIAnalysisEngine\Inference\DTO;

class Insight
{
    public string $id;
    public string $type;
    public string $headline;
    public string $summary;
    public string $details;
    public string $advice;
    public string $cta;
    public float $confidence;
    public int $importance;
    public string $visibility; // e.g. "free", "premium"
    public string $category;
    public array $matchedRule; // The raw rule data that matched
    public array $evidence; // Evidence items
    public array $remedies;
    public string $evidenceTrail;
    public ?string $unlockReason;

    public function __construct(
        string $id,
        string $type,
        string $headline,
        string $summary,
        string $details,
        string $advice,
        string $cta,
        float $confidence,
        int $importance,
        string $visibility,
        string $category,
        array $matchedRule,
        array $evidence,
        array $remedies = [],
        string $evidenceTrail = '',
        ?string $unlockReason = null
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->headline = $headline;
        $this->summary = $summary;
        $this->details = $details;
        $this->advice = $advice;
        $this->cta = $cta;
        $this->confidence = $confidence;
        $this->importance = $importance;
        $this->visibility = $visibility;
        $this->category = $category;
        $this->matchedRule = $matchedRule;
        $this->evidence = $evidence;
        $this->remedies = $remedies;
        $this->evidenceTrail = $evidenceTrail;
        $this->unlockReason = $unlockReason;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'headline' => $this->headline,
            'summary' => $this->summary,
            'details' => $this->details,
            'advice' => $this->advice,
            'cta' => $this->cta,
            'confidence' => $this->confidence,
            'importance' => $this->importance,
            'visibility' => $this->visibility,
            'category' => $this->category,
            'matchedRule' => $this->matchedRule,
            'evidence' => $this->evidence,
            'remedies' => $this->remedies,
            'evidenceTrail' => $this->evidenceTrail,
            'unlock_reason' => $this->unlockReason,
        ];
    }
}
