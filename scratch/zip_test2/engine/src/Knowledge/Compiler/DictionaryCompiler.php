<?php

namespace AIAnalysisEngine\Knowledge\Compiler;

use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;

class DictionaryCompiler implements CompilerInterface
{
    public function name(): string
    {
        return 'DictionaryCompiler';
    }

    public function execute(PipelineContext $context): PipelineResult
    {
        /** @var CompilerContext $context */
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        $result = new CompilerResult();
        $result->module = $this->name();

        if ($context->logger) {
            $context->logger->info('Loading dictionaries', [
                'module' => $this->name()
            ]);
        }

        try {
            $manifest = $context->pack->manifest;
            $basePath = rtrim($context->knowledgeContext->getRootPath(), '/') . '/dictionary';
            
            $declared = $context->registry->getDeclaredDictionaries($manifest, $basePath);
            
            $totalEntries = 0;
            $largestName = '';
            $largestSize = -1;

            foreach ($declared as $name => $data) {
                $context->pack->dictionaries->add($name, $data);
                
                $count = $this->countEntries($data);
                $totalEntries += $count;
                
                if ($count > $largestSize) {
                    $largestSize = $count;
                    $largestName = $name;
                }
                
                if ($context->logger) {
                    $context->logger->info('Dictionary compiled', [
                        'module' => $this->name(),
                        'name' => $name,
                        'entries' => $count
                    ]);
                }
            }

            if ($context->logger) {
                $context->logger->info('Dictionary compilation complete', [
                    'module' => $this->name()
                ]);
            }

            $result->statistics = [
                'dictionary_count' => count($declared),
                'entry_count' => $totalEntries,
                'largest_dictionary' => [
                    'name' => $largestName,
                    'entries' => $largestSize >= 0 ? $largestSize : 0
                ]
            ];
            
            $result->success = true;

        } catch (\Exception $e) {
            $result->success = false;
            $result->errors[] = $e->getMessage();
            
            if ($context->logger) {
                $context->logger->error('Dictionary compilation failed', [
                    'module' => $this->name(),
                    'error' => $e->getMessage()
                ]);
            }
        }

        $result->executionTime = microtime(true) - $startTime;
        $result->memoryUsage = memory_get_usage() - $startMemory;

        return $result;
    }

    private function countEntries(array $data): int
    {
        // Simple recursive count of leaf nodes or array structures
        $count = 0;
        foreach ($data as $value) {
            if (is_array($value)) {
                $count += $this->countEntries($value);
            } else {
                $count++;
            }
        }
        return $count === 0 && !empty($data) ? count($data) : $count;
    }
}
