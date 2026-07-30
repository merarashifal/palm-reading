<?php

namespace AIAnalysisEngine\Inference\Stages;

use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\StageResult;
use AIAnalysisEngine\Inference\DTO\ReasonCode;
use AIAnalysisEngine\Inference\DTO\CandidateStatus;
use AIAnalysisEngine\Inference\DTO\Candidate;

class ConflictResolver implements PipelineStageInterface
{
    public function name(): string
    {
        return 'ConflictResolver';
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
            $discarded = 0;

            // Group by Feature to resolve conflicts per anatomical feature
            $grouped = $context->candidates->groupByFeature();

            foreach ($grouped as $feature => $candidates) {
                /** @var Candidate[] $candidates */
                if (count($candidates) === 1) {
                    $newCand = $candidates[0]->withStatus(CandidateStatus::ACTIVE, $this->name(), ReasonCode::DEFAULT_MATCH);
                    $context->candidates->add($newCand);
                    $processed++;
                    $modified++;
                    continue;
                }
                
                // Find highest scorer (match + inference)
                usort($candidates, fn($a, $b) => 
                    ($b->matchScore + $b->inferenceScore) <=> ($a->matchScore + $a->inferenceScore)
                );

                $winner = $candidates[0];
                $newWinner = $winner->withStatus(CandidateStatus::ACTIVE, $this->name(), ReasonCode::HIGHER_PRIORITY);
                $context->candidates->add($newWinner);
                $processed++;
                $modified++;

                // Override the rest
                for ($i = 1; $i < count($candidates); $i++) {
                    $loser = $candidates[$i];
                    $newLoser = $loser->withStatus(CandidateStatus::OVERRIDDEN, $this->name(), ReasonCode::HIGHER_PRIORITY);
                    $context->candidates->add($newLoser);
                    $processed++;
                    $modified++;
                    $discarded++;
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
