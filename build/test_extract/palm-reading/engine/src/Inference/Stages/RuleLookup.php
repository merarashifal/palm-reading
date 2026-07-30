<?php

namespace AIAnalysisEngine\Inference\Stages;

use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\MatchedRule;
use AIAnalysisEngine\Inference\DTO\EvidenceCollection;

class RuleLookup implements PipelineStageInterface
{
    public function name(): string
    {
        return 'RuleLookup';
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
            $rulesReturned = 0;
            
            // Loop through all evidence and lookup in O(1) index
            foreach ($context->evidence as $evidence) {
                // RuleIndex returns CompiledRuleCollection
                $compiledRules = $context->knowledge->rules->index->match($evidence);
                
                foreach ($compiledRules as $compiledRule) {
                    $evCollection = new EvidenceCollection();
                    $evCollection->add($evidence);

                    $match = new MatchedRule(
                        $compiledRule,
                        $evCollection, // The evidence that triggered this
                        1.0,           // Default exact match confidence
                        [$evidence->feature], // Matched features
                        true,          // exactMatch
                        MatchedRule::STRATEGY_EXACT // strategy
                    );

                    $context->matchedRules->add($match);
                    $rulesReturned++;
                }
                
                $itemsProcessed++;
            }

            $context->trace->record(
                $this->name(),
                $startTime,
                microtime(true),
                $startMemory,
                memory_get_usage(),
                $itemsProcessed, // Evidence processed
                memory_get_peak_usage()
            );
            
            $result->statistics['rules_searched'] = $itemsProcessed;
            $result->statistics['rules_returned'] = $rulesReturned;

            $result->success = true;

        } catch (\Exception $e) {
            $result->success = false;
            $result->errors[] = $e->getMessage();
        }

        $result->executionTime = microtime(true) - $startTime;
        return $result;
    }
}
