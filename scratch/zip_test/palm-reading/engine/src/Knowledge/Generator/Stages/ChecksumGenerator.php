<?php

namespace AIAnalysisEngine\Knowledge\Generator\Stages;

use AIAnalysisEngine\Knowledge\Generator\BuildContext;
use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;

class ChecksumGenerator implements PipelineStageInterface
{
    public function name(): string
    {
        return 'ChecksumGenerator';
    }

    public function execute(PipelineContext $context): PipelineResult
    {
        /** @var BuildContext $context */
        $startTime = microtime(true);
        $result = new PipelineResult();
        $result->module = $this->name();

        try {
            $files = [
                'knowledge_pack.json',
                'install.sql',
                'knowledge_pack.csv',
                'diagnostics.json',
                'build.json'
            ];

            $checksums = [];
            $masterString = '';

            foreach ($files as $file) {
                $path = $context->latestDir . '/' . $file;
                if (file_exists($path)) {
                    $hash = hash_file('sha256', $path);
                    $checksums[$file] = $hash;
                    $masterString .= $hash;
                }
            }

            $masterChecksum = hash('sha256', $masterString);
            
            $output = [
                'master_checksum' => $masterChecksum,
                'files' => $checksums
            ];

            $jsonContent = json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            file_put_contents($context->latestDir . '/checksums.json', $jsonContent);
            file_put_contents($context->historyDir . '/checksums.json', $jsonContent);

            $result->success = true;

        } catch (\Exception $e) {
            $result->success = false;
            $result->errors[] = $e->getMessage();
        }

        $result->executionTime = microtime(true) - $startTime;
        return $result;
    }
}
