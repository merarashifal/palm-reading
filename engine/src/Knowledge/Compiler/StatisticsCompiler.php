<?php

namespace AIAnalysisEngine\Knowledge\Compiler;

use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;
use AIAnalysisEngine\Knowledge\Compiled\CompiledRule;

class StatisticsCompiler implements PipelineStageInterface
{
    public function name(): string
    {
        return 'StatisticsCompiler';
    }

    public function execute(PipelineContext $context): PipelineResult
    {
        /** @var CompilerContext $context */
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        $result = new PipelineResult();
        $result->module = $this->name();

        try {
            $rules = $context->pack->rules;
            
            $uniqueFeatures = [];
            $uniqueSections = [];
            $uniqueAnalyses = [];
            $uniqueLanguages = [];
            $uniqueVisibilities = [];
            $translationCount = 0;

            if ($rules) {
                /** @var CompiledRule $rule */
                foreach ($rules as $rule) {
                    $uniqueFeatures[$rule->feature] = true;
                    $uniqueSections[$rule->section] = true;
                    $uniqueAnalyses[$rule->analysis] = true;
                    $uniqueLanguages[$rule->language] = true;
                    $uniqueVisibilities[$rule->visibility] = true;
                    $translationCount += count($rule->translations);
                }
            }

            // Dictionary aggregates
            $dictionaryCount = count($context->pack->dictionaries->all());

            // Idempotent: Overwrite completely
            $context->pack->statistics = [
                'rules' => $rules ? $rules->count() : 0,
                'features' => count($uniqueFeatures),
                'sections' => count($uniqueSections),
                'analyses' => count($uniqueAnalyses),
                'languages' => count($uniqueLanguages),
                'visibilities' => count($uniqueVisibilities),
                'translations' => $translationCount,
                'dictionaries' => $dictionaryCount,
            ];

            if ($context->logger) {
                $context->logger->info("Statistics compiled", $context->pack->statistics);
            }

            $result->success = true;

        } catch (\Exception $e) {
            $result->success = false;
            $result->errors[] = $e->getMessage();
            if ($context->logger) {
                $context->logger->error("Statistics compilation failed: " . $e->getMessage());
            }
        }

        $result->executionTime = microtime(true) - $startTime;
        $result->memoryUsage = memory_get_usage() - $startMemory;

        return $result;
    }
}
