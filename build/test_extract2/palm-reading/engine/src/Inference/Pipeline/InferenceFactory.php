<?php

namespace AIAnalysisEngine\Inference\Pipeline;

use AIAnalysisEngine\Inference\Stages\FeatureHydrator;
use AIAnalysisEngine\Inference\Stages\EvidenceBuilder;
use AIAnalysisEngine\Inference\Stages\RuleLookup;
use AIAnalysisEngine\Inference\Stages\CandidateBuilder;
use AIAnalysisEngine\Inference\Stages\MatchScorer;
use AIAnalysisEngine\Inference\Stages\InferenceScorer;
use AIAnalysisEngine\Inference\Stages\ConflictResolver;
use AIAnalysisEngine\Inference\Stages\CandidateFilter;
use AIAnalysisEngine\Inference\Stages\RelationshipExpander;
use AIAnalysisEngine\Inference\Stages\ConfidenceResolver;
use AIAnalysisEngine\Inference\Stages\InferenceAssembler;

class InferenceFactory
{
    /**
     * Creates the fully assembled Inference Pipeline.
     */
    public static function createPipeline(): InferencePipeline
    {
        $pipeline = new InferencePipeline();
        
        $pipeline->register(new FeatureHydrator(), 10);
        $pipeline->register(new EvidenceBuilder(), 20);
        $pipeline->register(new RuleLookup(), 30);
        $pipeline->register(new CandidateBuilder(), 40);
        $pipeline->register(new MatchScorer(), 50);
        $pipeline->register(new InferenceScorer(), 60);
        $pipeline->register(new ConflictResolver(), 70);
        $pipeline->register(new CandidateFilter(), 80);
        $pipeline->register(new RelationshipExpander(), 90);
        $pipeline->register(new ConfidenceResolver(), 100);
        $pipeline->register(new InferenceAssembler(), 110);

        return $pipeline;
    }
}
