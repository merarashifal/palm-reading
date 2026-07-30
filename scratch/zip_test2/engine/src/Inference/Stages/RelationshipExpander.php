<?php

namespace AIAnalysisEngine\Inference\Stages;

use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\StageResult;
use AIAnalysisEngine\Inference\DTO\Evidence;

class RelationshipExpander implements PipelineStageInterface
{
    public function name(): string
    {
        return 'RelationshipExpander';
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

            // Notice: The expander reads from Evidence and outputs Derived Evidence.
            // It does not evaluate candidates.
            
            // To prevent infinite recursion, we only process non-derived evidence currently in the collection
            $baseEvidence = [];
            foreach ($context->evidence as $ev) {
                if (!$ev->isDerived) {
                    $baseEvidence[] = $ev;
                }
            }

            foreach ($baseEvidence as $ev) {
                $processed++;
                // In a real system, we'd query the relationship graph using the evidence values
                // For demonstration of architecture, we inject dummy derived evidence
                
                if ($context->configuration->allowExperimental && count($baseEvidence) > 1) {
                    $derivedId = 'derived_' . uniqid();
                    $derivedEvidence = new Evidence(
                        $derivedId,
                        'relationship_graph',
                        'complex_trait',
                        0.75, // Derived AI confidence penalty
                        50, // Lower weight for derived
                        true // isDerived
                    );

                    $context->evidence->add($derivedEvidence);
                    $modified++;
                    
                    // We only do this once to avoid combinatorial explosion in dummy logic
                    break;
                }
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
