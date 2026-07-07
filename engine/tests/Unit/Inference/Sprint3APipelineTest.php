<?php

namespace AIAnalysisEngine\Tests\Unit\Inference;

use PHPUnit\Framework\TestCase;
use AIAnalysisEngine\Tests\Fixtures\FixtureFactory;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\PermissionContext;
use AIAnalysisEngine\Inference\DTO\EngineConfiguration;
use AIAnalysisEngine\Inference\Pipeline\InferenceFactory;
use AIAnalysisEngine\Inference\DTO\Feature;
use AIAnalysisEngine\Inference\DTO\Evidence;

class Sprint3APipelineTest extends TestCase
{
    public function testDataIngestionAndEvidenceBuilding()
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
                'confidence' => 0.88,
                'source' => 'gemini_vision'
            ],
            [
                'analysis' => 'heart_line',
                'feature' => 'heart_line',
                'value' => 'chained',
                'confidence' => 0.95,
                'source' => 'gemini_vision'
            ]
        ];

        $context = new InferenceContext($rawPayload, $knowledge, $permission, $config);
        $pipeline = InferenceFactory::createPipeline();

        // 2. Act
        $results = $pipeline->execute($context);

        // 3. Assert Results
        $this->assertCount(2, $results);
        foreach ($results as $result) {
            $this->assertTrue($result->success, "Stage {$result->module} failed.");
        }

        // 4. Assert FeatureHydrator side effects
        $this->assertCount(2, $context->features);
        
        /** @var Feature[] $features */
        $features = array_values($context->features->all());
        $this->assertEquals('life_line', $features[0]->feature);
        $this->assertEquals('broken', $features[0]->value);
        $this->assertEquals(0.88, $features[0]->confidence);

        $this->assertEquals('heart_line', $features[1]->feature);
        $this->assertEquals('chained', $features[1]->value);
        $this->assertEquals(0.95, $features[1]->confidence);

        // 5. Assert EvidenceBuilder side effects
        $this->assertCount(2, $context->evidence);
        
        /** @var Evidence[] $evidences */
        $evidences = array_values($context->evidence->all());
        $this->assertEquals('life_line', $evidences[0]->feature);
        $this->assertEquals('broken', $evidences[0]->value);
        $this->assertEquals(0.88, $evidences[0]->aiConfidence);
        $this->assertEquals(100, $evidences[0]->weight); // Default baseline
        $this->assertFalse($evidences[0]->isDerived);

        // 6. Assert ExecutionTrace
        $trace = $context->trace->getStages();
        $this->assertCount(2, $trace);
        $this->assertEquals('FeatureHydrator', $trace[0]['stage']);
        $this->assertEquals(2, $trace[0]['items_processed']);
        
        $this->assertEquals('EvidenceBuilder', $trace[1]['stage']);
        $this->assertEquals(2, $trace[1]['items_processed']);
        
        // Assert execution time tracking exists
        $this->assertGreaterThan(0, $trace[0]['duration_ms']);
    }
}
