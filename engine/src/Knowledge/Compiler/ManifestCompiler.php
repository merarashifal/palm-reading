<?php

namespace AIAnalysisEngine\Knowledge\Compiler;

use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;

class ManifestCompiler implements CompilerInterface
{
    public function name(): string
    {
        return 'ManifestCompiler';
    }

    public function execute(PipelineContext $context): PipelineResult
    {
        /** @var CompilerContext $context */
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $result = new CompilerResult();
        $result->module = $this->name();

        if ($context->logger) {
            $context->logger->info('Compiling manifest', [
                'module' => $this->name()
            ]);
        }

        try {
            $manifest = $context->knowledgeContext->getManifest();

            // Normalize Languages
            $languages = $manifest['languages'] ?? [];
            $languages = array_map('strtolower', $languages);
            $languages = array_unique($languages);
            sort($languages);
            $manifest['languages'] = $languages;

            // Normalize Features
            $features = $manifest['features'] ?? [];
            $features = array_map('trim', $features);
            $features = array_map('strtolower', $features);
            $features = array_unique($features);
            sort($features);
            $manifest['features'] = $features;

            // Normalize Entry Points
            $entryPoints = $manifest['entry_points'] ?? [];
            $entryPoints = array_map(function ($path) {
                return str_replace('\\', '/', $path);
            }, $entryPoints);
            $entryPoints = array_unique($entryPoints);
            sort($entryPoints);
            $manifest['entry_points'] = $entryPoints;

            // Normalize Dictionaries
            $dictionaries = $manifest['dictionaries'] ?? [];
            $dictionaries = array_map('trim', $dictionaries);
            $dictionaries = array_map('strtolower', $dictionaries);
            $dictionaries = array_unique($dictionaries);
            sort($dictionaries);
            $manifest['dictionaries'] = $dictionaries;

            $context->pack->manifest = $manifest;

            // Populate Statistics
            $result->statistics = [
                'languages' => count($languages),
                'features' => count($features),
                'entry_points' => count($entryPoints),
                'dictionaries' => count($dictionaries)
            ];

            if ($context->logger) {
                $context->logger->info('Manifest Complete', [
                    'module' => $this->name(),
                    'languages_count' => count($languages),
                    'features_count' => count($features),
                    'entry_points_count' => count($entryPoints),
                    'dictionaries_count' => count($dictionaries)
                ]);
            }

            $result->success = true;
        } catch (\Exception $e) {
            $result->success = false;
            $result->errors[] = $e->getMessage();
            
            if ($context->logger) {
                $context->logger->error('Manifest compilation failed', [
                    'module' => $this->name(),
                    'error' => $e->getMessage()
                ]);
            }
        }

        $result->executionTime = microtime(true) - $startTime;
        $result->memoryUsage = memory_get_usage() - $startMemory;

        return $result;
    }
}
