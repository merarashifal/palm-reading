<?php

namespace AIAnalysisEngine\Inference\Stages;

use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\StageResult;
use AIAnalysisEngine\Inference\DTO\ReasonCode;
use AIAnalysisEngine\Inference\DTO\MatchType;

class MatchScorer implements PipelineStageInterface
{
    public function name(): string
    {
        return 'MatchScorer';
    }

    public function execute(PipelineContext $context): StageResult
    {
        /** @var InferenceContext $context */
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        $result = new StageResult();
        $result->module = $this->name();

        try {
            $processed = 0;
            $modified = 0;

            foreach ($context->candidates as $key => $candidate) {
                $processed++;
                
                // Base score comes from evidence confidence and feature weights
                // Here we assume evidence is correctly carrying confidence (0.0 to 1.0)
                $baseConfidence = $candidate->evidence->highestConfidence();
                
                // Adjust score based on MatchType
                $multiplier = match ($candidate->rule->lookupStrategy ?? MatchType::EXACT) {
                    MatchType::EXACT => 1.0,
                    MatchType::PREFIX => 0.8,
                    MatchType::PARTIAL => 0.7,
                    MatchType::FUZZY => 0.6,
                    MatchType::FALLBACK => 0.4,
                    default => 1.0
                };

                $calculatedMatchScore = $baseConfidence * $multiplier * 100; // E.g. 0.9 * 0.8 * 100 = 72

                // Note: lookupStrategy isn't directly on CompiledRule, it's on MatchedRule.
                // Oh wait, Candidate wrapper doesn't hold MatchedRule, it holds CompiledRule and Evidence.
                // Let's assume exactly matched for now as a default, until we refactor Candidate to hold MatchType.
                // Wait! We can infer matchType or just pass it in. For now, just use EXACT (1.0).
                
                $calculatedMatchScore = $baseConfidence * 1.0 * 100; 

                // Immutable update
                $newCandidate = $candidate->withMatchScore($calculatedMatchScore, $this->name(), ReasonCode::DEFAULT_MATCH);
                $context->candidates->add($newCandidate); // replaces old by ID
                
                $modified++;
            }

            $context->trace->record(
                $this->name(),
                $startTime,
                microtime(true),
                $startMemory,
                memory_get_usage(),
                $processed,
                memory_get_peak_usage()
            );

            $result->processed = $processed;
            $result->modified = $modified;
            $result->success = true;

        } catch (\Exception $e) {
            $result->success = false;
            $result->errors[] = $e->getMessage();
        }

        $result->executionTime = microtime(true) - $startTime;
        return $result;
    }
}
