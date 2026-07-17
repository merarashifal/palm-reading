<?php

namespace AIAnalysisEngine\Presentation\DTO;

class ReportDocument
{
    /** @var ReportComponent[] */
    public array $components = [];

    // Optional metadata for document-level properties, like user's name or theme request
    public array $metadata = [];

    public function __construct(array $components = [], array $metadata = [])
    {
        $this->components = $components;
        $this->metadata = $metadata;
    }

    public function addComponent(ReportComponent $component): void
    {
        $this->components[] = $component;
    }

    public function toArray(): array
    {
        return [
            'components' => array_map(fn($c) => $c->toArray(), $this->components),
            'metadata' => $this->metadata,
        ];
    }
}
