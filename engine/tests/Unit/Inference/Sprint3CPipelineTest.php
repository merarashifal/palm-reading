<?php

namespace AIAnalysisEngine\Tests\Unit\Inference;

use PHPUnit\Framework\TestCase;
use AIAnalysisEngine\Tests\Fixtures\FixtureFactory;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\PermissionContext;
use AIAnalysisEngine\Inference\DTO\EngineConfiguration;
use AIAnalysisEngine\Inference\Pipeline\InferenceFactory;
use AIAnalysisEngine\Inference\DTO\CandidateStatus;
use AIAnalysisEngine\Inference\DTO\Candidate;

class Sprint3CPipelineTest extends TestCase
{
    public function testScoringAndConflictResolution()
    {
        // 1. Arrange
        $knowledge = FixtureFactory::createSmall();
        $permission = new PermissionContext();
        $config = new EngineConfiguration();
        
        $rawPayload = [
            [
                'analysis' => 'life_line',
                'feature' => 'life_line',
                'value' => 'broken',
                'confidence' => 0.9
            ],
            [
                'analysis' => 'heart_line',
                'feature' => 'heart_line',
                'value' => 'chained',
                'confidence' => 0.8
            ]
        ];

        $context = new InferenceContext($rawPayload, $knowledge, $permission, $config);
        $pipeline = InferenceFactory::createPipeline();

        // 2. Act
        $results = $pipeline->execute($context);

        // 3. Assert Results
        $this->assertCount(8, $results); // 8 stages total now
        foreach ($results as $result) {
            $this->assertTrue($result->success, "Stage {$result->module} failed.");
        }

        // 4. Assert Filtering & Conflict Resolution worked
        // After CandidateFilter, there should be ONLY ACTIVE or MERGED candidates.
        foreach ($context->candidates as $candidate) {
            /** @var Candidate $candidate */
            $this->assertContains($candidate->status, [CandidateStatus::ACTIVE, CandidateStatus::MERGED]);
            
            // Assert scores were populated
            $this->assertGreaterThan(0.0, $candidate->matchScore);
            $this->assertGreaterThan(0.0, $candidate->inferenceScore);
            
            // Assert history is tracked
            $this->assertNotEmpty($candidate->history);
        }

        // 5. Assert Trace
        $trace = $context->trace->getStages();
        $this->assertCount(8, $trace);
        
        $this->assertEquals('MatchScorer', $trace[4]['stage']);
        $this->assertEquals('InferenceScorer', $trace[5]['stage']);
        $this->assertEquals('ConflictResolver', $trace[6]['stage']);
        $this->assertEquals('CandidateFilter', $trace[7]['stage']);
    }
}
