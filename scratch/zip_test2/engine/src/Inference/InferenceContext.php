<?php

namespace AIAnalysisEngine\Inference;

use AIAnalysisEngine\Knowledge\Compiled\CompiledKnowledgePack;
use AIAnalysisEngine\Inference\DTO\FeatureCollection;
use AIAnalysisEngine\Inference\DTO\EvidenceCollection;
use AIAnalysisEngine\Inference\DTO\CandidateCollection;
use AIAnalysisEngine\Inference\DTO\InferenceResult;
use AIAnalysisEngine\Inference\DTO\PermissionContext;
use AIAnalysisEngine\Inference\DTO\EngineConfiguration;
use AIAnalysisEngine\Inference\DTO\ExecutionTrace;
use AIAnalysisEngine\Pipeline\PipelineContext;

class InferenceContext extends PipelineContext
{
    public array $rawPayload;
    public CompiledKnowledgePack $knowledge;
    public FeatureCollection $features;
    public EvidenceCollection $evidence;
    public \AIAnalysisEngine\Inference\DTO\MatchedRuleCollection $matchedRules;
    public CandidateCollection $candidates;
    public InferenceResult $result;
    public PermissionContext $permission;
    public EngineConfiguration $configuration;
    public ExecutionTrace $trace;

    public function __construct(
        array $rawPayload,
        CompiledKnowledgePack $knowledge,
        PermissionContext $permission,
        EngineConfiguration $configuration
    ) {
        $this->rawPayload = $rawPayload;
        $this->knowledge = $knowledge;
        $this->permission = $permission;
        $this->configuration = $configuration;
        
        $this->features = new FeatureCollection();
        $this->evidence = new EvidenceCollection();
        $this->matchedRules = new \AIAnalysisEngine\Inference\DTO\MatchedRuleCollection();
        $this->candidates = new CandidateCollection();
        $this->result = new InferenceResult($permission->subscription);
        $this->trace = new ExecutionTrace();
    }
}
