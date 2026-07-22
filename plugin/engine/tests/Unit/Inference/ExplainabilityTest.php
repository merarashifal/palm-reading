<?php

namespace AIAnalysisEngine\Tests\Unit\Inference;

use PHPUnit\Framework\TestCase;
use AIAnalysisEngine\Tests\Fixtures\FixtureFactory;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\PermissionContext;
use AIAnalysisEngine\Inference\DTO\EngineConfiguration;
use AIAnalysisEngine\Inference\Pipeline\InferenceFactory;

class ExplainabilityTest extends TestCase
{
    public function testExplainabilityReportGeneration()
    {
        $knowledge = FixtureFactory::createSmall();
        $permission = new PermissionContext();
        $config = new EngineConfiguration();

        $rawPayload = [
            ['analysis' => 'life_line', 'feature' => 'life_line', 'value' => 'broken', 'confidence' => 0.9],
            ['analysis' => 'heart_line', 'feature' => 'heart_line', 'value' => 'chained', 'confidence' => 0.8]
        ];

        $context = new InferenceContext($rawPayload, $knowledge, $permission, $config);
        $pipeline = InferenceFactory::createPipeline();
        $pipeline->execute($context);

        $report = [];

        foreach ($context->candidates as $candidate) {
            $historyOutput = [];
            foreach ($candidate->history as $entry) {
                $historyOutput[] = [
                    'stage' => $entry->stage,
                    'action' => $entry->action,
                    'reason' => $entry->reason->name,
                    'timestamp' => $entry->timestamp
                ];
            }

            $report[] = [
                'candidate_id' => $candidate->id,
                'rule_uid' => $candidate->rule->uid,
                'status' => $candidate->status->name,
                'match_score' => $candidate->matchScore,
                'inference_score' => $candidate->inferenceScore,
                'history' => $historyOutput
            ];
        }

        $json = json_encode(['explainability' => $report], JSON_PRETTY_PRINT);
        
        $outputDir = __DIR__ . '/../../../release';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }
        
        file_put_contents($outputDir . '/explainability_report.json', $json);
        
        $this->assertFileExists($outputDir . '/explainability_report.json');
        
        // Assert invariants in the report
        foreach ($report as $candReport) {
            $this->assertNotEmpty($candReport['history'], "Candidate {$candReport['candidate_id']} has empty history.");
        }
    }
}
