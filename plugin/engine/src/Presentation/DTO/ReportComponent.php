<?php

namespace AIAnalysisEngine\Presentation\DTO;

class ReportComponent
{
    public string $id;
    public string $type;         // e.g. Hero, Metric, Section, Quote, Timeline, Checklist, CTA, Divider, Gallery, Evidence
    public string $title;
    public string $subtitle;
    public string $icon;
    public string $layout;       // e.g. cards, list, grid
    public string $style;        // e.g. hero, premium, default
    public string $visibility;   // e.g. free, premium
    public int $importance;
    public string $analytics_id; // For BI tracking (e.g. hero_001, cta_unlock)
    public array $data;          // Flexible payload (e.g. insights, checklist items, text blocks)

    public function __construct(
        string $id,
        string $type,
        string $title = '',
        string $subtitle = '',
        string $icon = '',
        string $layout = 'default',
        string $style = 'default',
        string $visibility = 'free',
        int $importance = 50,
        string $analytics_id = '',
        array $data = []
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->icon = $icon;
        $this->layout = $layout;
        $this->style = $style;
        $this->visibility = $visibility;
        $this->importance = $importance;
        $this->analytics_id = $analytics_id;
        $this->data = $data;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'icon' => $this->icon,
            'layout' => $this->layout,
            'style' => $this->style,
            'visibility' => $this->visibility,
            'importance' => $this->importance,
            'analytics_id' => $this->analytics_id,
            'data' => $this->data,
        ];
    }
}
