<?php

namespace AIAnalysisEngine\Inference\Stages;

use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\Feature;

class FeatureHydrator implements PipelineStageInterface
{
    public function name(): string
    {
        return 'FeatureHydrator';
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
            
            // Assume rawPayload is an array of feature definitions like:
            // [['id' => 'f1', 'analysis' => 'life_line', 'feature' => 'life_line', 'value' => 'broken', 'confidence' => 0.9], ...]
            foreach ($context->rawPayload as $raw) {
                if (!isset($raw['analysis'], $raw['feature'], $raw['value'])) {
                    continue; // Skip invalid
                }

                $id = $raw['id'] ?? uniqid('feat_');
                $confidence = isset($raw['confidence']) ? (float)$raw['confidence'] : 1.0;
                $source = $raw['source'] ?? 'vision';
                $metadata = $raw['metadata'] ?? [];

                $feature = new Feature(
                    $id,
                    $raw['analysis'],
                    $raw['feature'],
                    $raw['value'],
                    $confidence,
                    $source,
                    $metadata
                );

                $context->features->add($feature);
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
