<?php

namespace AIAnalysisEngine\Inference\Stages;

use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\Evidence;

class EvidenceBuilder implements PipelineStageInterface
{
    public function name(): string
    {
        return 'EvidenceBuilder';
    }

    public function execute(PipelineContext $context): PipelineResult
    {
        /** @var InferenceContext $context */
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        $result = new PipelineResult();
        $result->module = $this->name();

        try {
            $itemsProcessed = 0;
            
            // As per architecture freeze: 
            // EvidenceBuilder is explicitly simple. No relationship logic, no weights.
            // Feature -> Evidence mapping only.
            foreach ($context->features as $feature) {
                // The Evidence ID usually mirrors the Feature ID to maintain traceability.
                $evidenceId = 'ev_' . $feature->id;
                
                $evidence = new Evidence(
                    $evidenceId,
                    $feature->feature,
                    $feature->value,
                    $feature->confidence,
                    100, // Default baseline weight, will be manipulated by Ranker later
                    false // Not derived
                );

                $context->evidence->add($evidence);
                $itemsProcessed++;
            }

            $context->trace->record(
                $this->name(),
                $startTime,
                microtime(true),
                $startMemory,
                memory_get_usage(),
                $itemsProcessed,
                memory_get_peak_usage()
            );

            $result->success = true;

        } catch (\Exception $e) {
            $result->success = false;
            $result->errors[] = $e->getMessage();
        }

        $result->executionTime = microtime(true) - $startTime;
        return $result;
    }
}
