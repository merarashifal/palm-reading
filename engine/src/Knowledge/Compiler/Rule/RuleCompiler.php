<?php

namespace AIAnalysisEngine\Knowledge\Compiler\Rule;

use AIAnalysisEngine\Knowledge\Compiler\CompilerInterface;
use AIAnalysisEngine\Knowledge\Compiler\CompilerContext;
use AIAnalysisEngine\Knowledge\Compiler\CompilerResult;
use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;

class RuleCompiler implements CompilerInterface
{
    public function name(): string
    {
        return 'RuleCompiler';
    }

    public function execute(PipelineContext $context): PipelineResult
    {
        /** @var CompilerContext $context */
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        $result = new CompilerResult();
        $result->module = $this->name();

        if ($context->logger) {
            $context->logger->info('Starting rule compilation', [
                'module' => $this->name()
            ]);
        }

        try {
            $loader = new RuleLoader();
            $normalizer = new RuleNormalizer();
            $collectionBuilder = new RuleCollectionBuilder();
            $optimizer = new RuleOptimizer();

            // 1. Load raw rule arrays from the registry based on manifest entry points
            $rawRules = $loader->load($context->registry, $context->pack->manifest, $context->knowledgeContext->getRootPath());
            
            // 2. Normalize and build the initial collection (which internally builds the index)
            $collection = $collectionBuilder->build($rawRules, $normalizer);
            
            // 3. Optimize (sort and deduplicate) returning a brand new, clean collection with rebuilt indexes
            $optimizedCollection = $optimizer->optimize($collection);
            
            // 4. Inject into the pack
            $context->pack->rules = $optimizedCollection;

            if ($context->logger) {
                $context->logger->info('Rule compilation complete', [
                    'module' => $this->name(),
                    'rules_processed' => count($rawRules),
                    'rules_optimized' => $optimizedCollection->count()
                ]);
            }

            $result->statistics = [
                'rules_processed' => count($rawRules),
                'rules_indexed' => $optimizedCollection->count(),
            ];
            
            $result->success = true;

        } catch (\Exception $e) {
            $result->success = false;
            $result->errors[] = $e->getMessage();
            
            if ($context->logger) {
                $context->logger->error('Rule compilation failed', [
                    'module' => $this->name(),
                    'error' => $e->getMessage()
                ]);
            }
        }

        $result->executionTime = microtime(true) - $startTime;
        $result->memoryUsage = memory_get_usage() - $startMemory;
        
        $result->statistics['compilation_time'] = $result->executionTime;
        $result->statistics['peak_memory'] = memory_get_peak_usage();

        return $result;
    }
}
