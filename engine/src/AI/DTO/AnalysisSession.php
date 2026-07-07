<?php

namespace AIAnalysisEngine\AI\DTO;

class AnalysisSession
{
    public string $id;
    public InputArtifact $artifact;
    
    // The master definition guiding this execution
    public AnalysisDefinition $definition;

    // Loaded Assets
    public ?Asset $prompt = null;
    public ?Asset $schema = null;
    public ?Asset $calibration = null;

    public ?ProviderIdentity $identity = null;
    public ProviderExecution $execution;
    public ?ProviderRequest $request = null;
    public ?ProviderResponse $response = null;
    public ?NormalizedFeatureCollection $features = null;
    
    public float $duration = 0.0;
    public array $warnings = [];
    public array $diagnostics = [];
    public bool $success = false;

    public function __construct(string $id, InputArtifact $artifact, AnalysisDefinition $definition)
    {
        $this->id = $id;
        $this->artifact = $artifact;
        $this->definition = $definition;
        $this->execution = new ProviderExecution();
    }

    public function recordTimeline(string $stage, float $durationMs): void
    {
        $this->diagnostics['timeline'][] = [
            'stage' => $stage,
            'duration_ms' => $durationMs
        ];
    }
}
