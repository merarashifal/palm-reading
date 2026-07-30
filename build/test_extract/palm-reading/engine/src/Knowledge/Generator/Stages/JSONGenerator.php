<?php

namespace AIAnalysisEngine\Knowledge\Generator\Stages;

use AIAnalysisEngine\Knowledge\Generator\BuildContext;
use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;

class JSONGenerator implements PipelineStageInterface
{
    public function name(): string
    {
        return 'JSONGenerator';
    }

    public function execute(PipelineContext $context): PipelineResult
    {
        /** @var BuildContext $context */
        $startTime = microtime(true);
        $result = new PipelineResult();
        $result->module = $this->name();

        try {
            $pack = $context->pack;

            // Serialize the entire CompiledKnowledgePack
            // We use standard PHP object serialization to JSON.
            // Since our collections use IteratorAggregate and contain pure DTOs,
            // we should manually format the output or use a custom serializer if needed.
            // For now, we will build the canonical array manually to ensure strict format.

            $canonical = [
                'manifest' => $pack->manifest,
                'metadata' => $pack->metadata,
                'statistics' => $pack->statistics,
                'dictionaries' => $pack->dictionaries->all(),
                'rules' => []
            ];

            foreach ($pack->rules as $rule) {
                // Ensure pure object properties are serialized
                $canonical['rules'][] = (array) $rule;
            }

            $jsonContent = json_encode($canonical, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($jsonContent === false) {
                throw new \Exception("Failed to encode Knowledge Pack to JSON: " . json_last_error_msg());
            }

            // Write to both latest and history
            $latestFile = $context->latestDir . '/knowledge_pack.json';
            $historyFile = $context->historyDir . '/knowledge_pack.json';

            file_put_contents($latestFile, $jsonContent);
            file_put_contents($historyFile, $jsonContent);

            $result->success = true;
            $result->statistics['size_bytes'] = strlen($jsonContent);

        } catch (\Exception $e) {
            $result->success = false;
            $result->errors[] = $e->getMessage();
        }

        $result->executionTime = microtime(true) - $startTime;
        return $result;
    }
}
