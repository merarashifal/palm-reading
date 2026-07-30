<?php

namespace AIAnalysisEngine\Inference\Stages;

use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\StageResult;
use AIAnalysisEngine\Inference\DTO\ReasonCode;

class InferenceScorer implements PipelineStageInterface
{
    public function name(): string
    {
        return 'InferenceScorer';
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
                
                // Inference score focuses on knowledge strength (priority, domain weight)
                // E.g. base on rule priority
                $baseScore = (float) $candidate->rule->priority;
                
                // In a real scenario we'd add visibility bonus, etc.
                
                $calculatedInferenceScore = $baseScore;

                // Immutable update
                $newCandidate = $candidate->withInferenceScore($calculatedInferenceScore, $this->name(), ReasonCode::HIGHER_PRIORITY);
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
