<?php

namespace AIAnalysisEngine\Inference\Stages;

use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\Candidate;

class CandidateBuilder implements PipelineStageInterface
{
    public function name(): string
    {
        return 'CandidateBuilder';
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
            
            foreach ($context->matchedRules as $matchedRule) {
                // Each candidate gets a unique ID, ensuring identical rules triggered by different evidence are separate
                $candidateId = 'cand_' . uniqid('', true);
                
                // Defaults initialized by Candidate constructor (ACTIVE, 0 scores, empty confidence)
                $candidate = new Candidate(
                    $candidateId,
                    $matchedRule->rule,
                    $matchedRule->evidence
                );
                
                $context->candidates->add($candidate);
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
