<?php

namespace AIAnalysisEngine\Inference\DTO;

class InferenceResult
{
    /** @var array<string, Section> */
    public array $sections = [];
    public ConfidenceResult $confidence;
    public array $matchedRules = [];
    public array $reasoningPath = [];
    public string $visibilityContext;
    
    public string $buildVersion = 'unknown';
    public string $knowledgePackVersion = 'unknown';
    public string $engineVersion = 'unknown';
    public string $generatedAt;

    public InferenceStatistics $statistics;

    public function __construct(string $visibilityContext)
    {
        $this->confidence = new ConfidenceResult();
        $this->visibilityContext = $visibilityContext;
        $this->generatedAt = date('c');
        $this->statistics = new InferenceStatistics();
    }
}

class Section
{
    public string $name;
    /** @var Item[] */
    public array $items = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}

class Item
{
    public EvidenceCollection $evidence;
    public ConfidenceResult $confidence;
    public array $translations = [];

    public function __construct(EvidenceCollection $evidence)
    {
        $this->evidence = $evidence;
        $this->confidence = new ConfidenceResult();
    }
}
