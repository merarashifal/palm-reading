<?php

namespace AIAnalysisEngine\Knowledge\Generator\Stages;

use AIAnalysisEngine\Knowledge\Generator\BuildContext;
use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;
use AIAnalysisEngine\Pipeline\PipelineStageInterface;

class CSVGenerator implements PipelineStageInterface
{
    public function name(): string
    {
        return 'CSVGenerator';
    }

    public function execute(PipelineContext $context): PipelineResult
    {
        /** @var BuildContext $context */
        $startTime = microtime(true);
        $result = new PipelineResult();
        $result->module = $this->name();

        try {
            $path = $context->latestDir . '/knowledge_pack.csv';
            $historyPath = $context->historyDir . '/knowledge_pack.csv';

            $fp = fopen($path, 'w');
            if ($fp === false) {
                throw new \Exception("Could not open CSV file for writing.");
            }

            // Headers
            fputcsv($fp, [
                'UID', 'Language', 'Analysis', 'Feature', 'Value', 
                'Section', 'Visibility', 'Confidence', 'Priority'
            ]);

            foreach ($context->pack->rules as $rule) {
                fputcsv($fp, [
                    $rule->uid,
                    $rule->language,
                    $rule->analysis,
                    $rule->feature,
                    $rule->value,
                    $rule->section,
                    $rule->visibility,
                    $rule->confidence,
                    $rule->priority
                ]);
            }

            fclose($fp);
            copy($path, $historyPath);

            $result->success = true;

        } catch (\Exception $e) {
            $result->success = false;
            $result->errors[] = $e->getMessage();
        }

        $result->executionTime = microtime(true) - $startTime;
        return $result;
    }
}
