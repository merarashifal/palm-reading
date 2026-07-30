<?php

namespace AIAnalysisEngine\Knowledge\Generator\Stages;

use AIAnalysisEngine\Knowledge\Generator\BuildContext;
use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;

class BuildManifestGenerator implements PipelineStageInterface
{
    public function name(): string
    {
        return 'BuildManifestGenerator';
    }

    public function execute(PipelineContext $context): PipelineResult
    {
        /** @var BuildContext $context */
        $startTime = microtime(true);
        $result = new PipelineResult();
        $result->module = $this->name();

        try {
            $engineVersion = $context->pack->metadata['build']['engine_version'] ?? 'unknown';
            $packVersion = $context->pack->metadata['build']['knowledge_pack_version'] ?? 'unknown';

            $buildManifest = [
                'engine' => $engineVersion,
                'knowledge_pack' => $packVersion,
                'generated_at' => date('c'),
                'artifacts' => [
                    'knowledge_pack.json',
                    'install.sql',
                    'knowledge_pack.csv',
                    'diagnostics.json',
                    'checksums.json'
                ]
            ];

            $jsonContent = json_encode($buildManifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            file_put_contents($context->latestDir . '/build.json', $jsonContent);
            file_put_contents($context->historyDir . '/build.json', $jsonContent);

            $result->success = true;

        } catch (\Exception $e) {
            $result->success = false;
            $result->errors[] = $e->getMessage();
        }

        $result->executionTime = microtime(true) - $startTime;
        return $result;
    }
}
