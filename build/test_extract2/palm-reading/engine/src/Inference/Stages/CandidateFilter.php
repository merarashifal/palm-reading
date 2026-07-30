<?php

namespace AIAnalysisEngine\Inference\Stages;

use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\StageResult;
use AIAnalysisEngine\Inference\DTO\CandidateCollection;

class CandidateFilter implements PipelineStageInterface
{
    public function name(): string
    {
        return 'CandidateFilter';
    }

    public function execute(PipelineContext $context): StageResult
    {
        /** @var InferenceContext $context */
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        $result = new StageResult();
        $result->module = $this->name();

        try {
            $processed = count($context->candidates->all());
            $discarded = 0;

            $filteredCollection = new CandidateCollection();
            
            foreach ($context->candidates->active() as $active) {
                $filteredCollection->add($active);
            }
            foreach ($context->candidates->merged() as $merged) {
                $filteredCollection->add($merged);
            }

            $discarded = $processed - count($filteredCollection->all());
            
            // Replace the context collection with only the filtered winners
            $context->candidates = $filteredCollection;

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
            $result->modified = count($filteredCollection->all());
            $result->discarded = $discarded;
            $result->success = true;

        } catch (\Exception $e) {
            $result->success = false;
            $result->errors[] = $e->getMessage();
        }

        $result->executionTime = microtime(true) - $startTime;
        return $result;
    }
}
