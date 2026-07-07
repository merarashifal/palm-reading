<?php

namespace AIAnalysisEngine\Knowledge\Generator\Stages;

use AIAnalysisEngine\Knowledge\Generator\BuildContext;
use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;

class DiagnosticsGenerator implements PipelineStageInterface
{
    public function name(): string
    {
        return 'DiagnosticsGenerator';
    }

    public function execute(PipelineContext $context): PipelineResult
    {
        /** @var BuildContext $context */
        $startTime = microtime(true);
        $result = new PipelineResult();
        $result->module = $this->name();

        try {
            $diagnostics = [
                'rule_count' => $context->pack->rules->count(),
                'dictionary_count' => count($context->pack->dictionaries->all()),
                'artifacts' => []
            ];

            // Calculate artifact sizes
            $files = [
                'knowledge_pack.json',
                'install.sql',
                'knowledge_pack.csv'
            ];

            foreach ($files as $file) {
                $path = $context->latestDir . '/' . $file;
                if (file_exists($path)) {
                    $diagnostics['artifacts'][$file] = [
                        'size_bytes' => filesize($path),
                    ];
                }
            }

            $diagnostics['generation_duration'] = microtime(true) - $startTime;

            $jsonContent = json_encode($diagnostics, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            file_put_contents($context->latestDir . '/diagnostics.json', $jsonContent);
            file_put_contents($context->historyDir . '/diagnostics.json', $jsonContent);

            $result->success = true;

        } catch (\Exception $e) {
            $result->success = false;
            $result->errors[] = $e->getMessage();
        }

        $result->executionTime = microtime(true) - $startTime;
        return $result;
    }
}
