<?php

namespace AIAnalysisEngine\Knowledge\Compiler;

use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;

class MetadataCompiler implements PipelineStageInterface
{
    public function name(): string
    {
        return 'MetadataCompiler';
    }

    public function execute(PipelineContext $context): PipelineResult
    {
        /** @var CompilerContext $context */
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        $result = new PipelineResult();
        $result->module = $this->name();

        try {
            $manifest = $context->pack->manifest;

            // Idempotent: completely overwrite metadata
            $context->pack->metadata = [
                'build' => [
                    'engine_version' => $this->getEngineVersion($context),
                    'knowledge_pack_version' => $manifest['version'] ?? '1.0',
                    'compiled_at' => date('c'), // ISO 8601
                    'compiler_version' => '1.0'
                ]
            ];

            if ($context->logger) {
                $context->logger->info("Metadata compiled successfully", [
                    'pack_version' => $context->pack->metadata['build']['knowledge_pack_version']
                ]);
            }

            $result->success = true;

        } catch (\Exception $e) {
            $result->success = false;
            $result->errors[] = $e->getMessage();
            if ($context->logger) {
                $context->logger->error("Metadata compilation failed: " . $e->getMessage());
            }
        }

        $result->executionTime = microtime(true) - $startTime;
        $result->memoryUsage = memory_get_usage() - $startMemory;

        return $result;
    }

    private function getEngineVersion(CompilerContext $context): string
    {
        $versionFile = $context->knowledgeContext->getRootPath() . '/../engine/VERSION';
        if (file_exists($versionFile)) {
            return trim(file_get_contents($versionFile));
        }
        return '0.6.0'; // Fallback
    }
}
