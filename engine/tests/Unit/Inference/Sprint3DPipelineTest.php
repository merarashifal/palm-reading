<?php

namespace AIAnalysisEngine\Tests\Unit\Inference;

use PHPUnit\Framework\TestCase;
use AIAnalysisEngine\Tests\Fixtures\FixtureFactory;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\PermissionContext;
use AIAnalysisEngine\Inference\DTO\EngineConfiguration;
use AIAnalysisEngine\Inference\Pipeline\InferenceFactory;
use AIAnalysisEngine\Inference\DTO\InferenceResult;

class Sprint3DPipelineTest extends TestCase
{
    public function testEndToEndInference()
    {
        // 1. Arrange
        $knowledge = FixtureFactory::createSmall();
        $permission = new PermissionContext();
        $config = new EngineConfiguration();
        $config->allowExperimental = true; // Enables dummy relationship logic
        
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
        $this->assertCount(11, $results); // 11 stages total now
        foreach ($results as $result) {
            $this->assertTrue($result->success, "Stage {$result->module} failed.");
        }

        // 4. Assert Final InferenceResult
        $this->assertInstanceOf(InferenceResult::class, $context->result);
        
        // Assert serialization properties
        $this->assertNotEmpty($context->result->sections);
        $this->assertEquals($knowledge->metadata['build']['knowledge_pack_version'] ?? 'unknown', $context->result->buildVersion);
        
        // Ensure statistics were populated
        $this->assertGreaterThan(0, $context->result->statistics->totalExecutionTime);
        $this->assertGreaterThan(0, $context->result->statistics->rulesMatched);
        
        // Assert derived evidence creation
        $hasDerived = false;
        foreach ($context->evidence as $ev) {
            if ($ev->isDerived) {
                $hasDerived = true;
                break;
            }
        }
        $this->assertTrue($hasDerived, "RelationshipExpander failed to create derived evidence.");
    }
}
