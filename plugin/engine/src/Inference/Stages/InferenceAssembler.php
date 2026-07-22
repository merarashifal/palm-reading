<?php

namespace AIAnalysisEngine\Inference\Stages;

use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\StageResult;
use AIAnalysisEngine\Inference\DTO\Section;
use AIAnalysisEngine\Inference\DTO\Item;

class InferenceAssembler implements PipelineStageInterface
{
    public function name(): string
    {
        return 'InferenceAssembler';
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

            // Pure serializer stage. No sorting, no filtering.
            
            // Populate metadata
            $context->result->buildVersion = $context->knowledge->metadata['build']['knowledge_pack_version'] ?? 'unknown';
            $context->result->knowledgePackVersion = $context->knowledge->manifest['knowledge_pack_version'] ?? 'unknown';
            $context->result->engineVersion = $context->knowledge->manifest['engine_version'] ?? 'unknown';

            // Group strictly by section
            $groups = $context->candidates->groupBySection();

            foreach ($groups as $sectionName => $candidates) {
                $section = new Section($sectionName);
                
                foreach ($candidates as $candidate) {
                    $processed++;
                    
                    $item = new Item($candidate->evidence);
                    $item->confidence = $candidate->confidence;
                    
                    // Fetch translations if any (mocked here, we would get this from DictionaryCollection)
                    $item->translations['en'] = [
                        'title' => $candidate->rule->feature . ' matches ' . $candidate->rule->value,
                        'description' => 'Detailed analysis for ' . $candidate->rule->uid
                    ];

                    $section->items[] = $item;
                    $modified++;
                }

                $context->result->sections[$sectionName] = $section;
            }

            // Aggregate statistics
            $context->result->statistics->featuresReceived = count($context->features->all());
            $context->result->statistics->evidenceCreated = count($context->evidence->all());
            $context->result->statistics->rulesMatched = count($context->matchedRules->all());
            $context->result->statistics->candidatesActive = $context->candidates->countActive();
            $context->result->statistics->candidatesDiscarded = $context->candidates->countDiscarded();
            $context->result->statistics->candidatesMerged = $context->candidates->countMerged();
            $context->result->statistics->candidatesOverridden = $context->candidates->countOverridden();
            
            // Total execution time will be finalized by the Pipeline executor, but we can sum the trace
            $totalTime = 0;
            foreach ($context->trace->getStages() as $traceItem) {
                $totalTime += $traceItem['duration_ms'];
            }
            $context->result->statistics->totalExecutionTime = $totalTime;

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
