<?php

namespace AIAnalysisEngine\Tests\Golden;

use PHPUnit\Framework\TestCase;
use AIAnalysisEngine\Tests\Fixtures\FixtureFactory;
use AIAnalysisEngine\Inference\InferenceContext;
use AIAnalysisEngine\Inference\DTO\PermissionContext;
use AIAnalysisEngine\Inference\DTO\EngineConfiguration;
use AIAnalysisEngine\Inference\Pipeline\InferenceFactory;

class GoldenInferenceTest extends TestCase
{
    private string $goldenDir;

    protected function setUp(): void
    {
        $this->goldenDir = __DIR__ . '/../../../release/golden';
        if (!is_dir($this->goldenDir)) {
            mkdir($this->goldenDir, 0777, true);
        }
    }

    public function testSmallGolden()
    {
        $this->runGoldenScenario('small', FixtureFactory::createSmall(), [
            ['analysis' => 'life_line', 'feature' => 'life_line', 'value' => 'broken', 'confidence' => 0.9]
        ]);
    }

    public function testRelationshipsGolden()
    {
        $config = new EngineConfiguration();
        $config->allowExperimental = true;
        
        $this->runGoldenScenario('relationships', FixtureFactory::createSmall(), [
            ['analysis' => 'life_line', 'feature' => 'life_line', 'value' => 'broken', 'confidence' => 0.9],
            ['analysis' => 'head_line', 'feature' => 'head_line', 'value' => 'straight', 'confidence' => 0.8]
        ], $config);
    }

    private function runGoldenScenario(string $name, $knowledge, array $payload, ?EngineConfiguration $config = null)
    {
        $permission = new PermissionContext();
        $config = $config ?? new EngineConfiguration();

        $context = new InferenceContext($payload, $knowledge, $permission, $config);
        $pipeline = InferenceFactory::createPipeline();
        $pipeline->execute($context);

        // We only serialize the result, but we unset generatedAt because it changes every run
        $resultData = json_decode(json_encode($context->result), true);
        unset($resultData['generatedAt']);
        unset($resultData['statistics']['totalExecutionTime']);

        $currentJson = json_encode($resultData, JSON_PRETTY_PRINT);
        
        $goldenFile = $this->goldenDir . '/' . $name . '.json';
        
        if (!file_exists($goldenFile)) {
            // First run generates the golden file
            file_put_contents($goldenFile, $currentJson);
            $this->markTestSkipped("Generated golden file for {$name}. Run again to test.");
        } else {
            $goldenJson = file_get_contents($goldenFile);
            $this->assertJsonStringEqualsJsonString($goldenJson, $currentJson, "Golden output mismatch for scenario: {$name}");
        }
    }
}
