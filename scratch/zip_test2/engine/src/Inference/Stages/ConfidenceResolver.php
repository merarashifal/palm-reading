<?php

namespace AIAnalysisEngine\Inference\Stages;

use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\StageResult;
use AIAnalysisEngine\Inference\DTO\ConfidenceResult;
use AIAnalysisEngine\Inference\DTO\ReasonCode;

class ConfidenceResolver implements PipelineStageInterface
{
    public function name(): string
    {
        return 'ConfidenceResolver';
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

            foreach ($context->candidates as $candidate) {
                $processed++;
                
                // Pure math stage
                // AI Confidence from evidence
                $aiConfidence = $candidate->evidence->highestConfidence();
                
                // Inference Confidence derived from inferenceScore (normalized to 0-1)
                $inferenceConfidence = min(1.0, max(0.0, $candidate->inferenceScore / 100));
                
                // Relationship Bonus (e.g. if evidence contains derived components)
                $relationshipBonus = 0.0;
                foreach ($candidate->evidence as $ev) {
                    if ($ev->isDerived) {
                        $relationshipBonus += 0.1;
                    }
                }
                $relationshipBonus = min(1.0, $relationshipBonus);

                $confidence = new ConfidenceResult($aiConfidence, $inferenceConfidence, $relationshipBonus);
                
                // Final calculation happens internally inside ConfidenceResult
                
                $newCandidate = $candidate->withConfidence($confidence, $this->name(), ReasonCode::DEFAULT_MATCH);
                $context->candidates->add($newCandidate);

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
