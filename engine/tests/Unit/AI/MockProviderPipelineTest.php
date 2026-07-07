<?php

namespace AIAnalysisEngine\Tests\Unit\AI;

use PHPUnit\Framework\TestCase;
use AIAnalysisEngine\AI\Providers\Mock\MockProviderPipeline;
use AIAnalysisEngine\AI\DTO\AnalysisSession;
use AIAnalysisEngine\AI\DTO\AnalysisDefinition;
use AIAnalysisEngine\AI\DTO\ImageArtifact;

class MockProviderPipelineTest extends TestCase
{
    public function testMockPipelineExecution()
    {
        $artifact = new ImageArtifact('/tmp/mock.jpg', 'image/jpeg');
        $definition = new AnalysisDefinition('uuid-123', 'MockAnalysis', '1.0', 'p1', 's1', 'c1', 'pre1', 'prov1');
        $session = new AnalysisSession('sess_123', $artifact, $definition);
        
        $pipeline = new MockProviderPipeline();
        $resultSession = $pipeline->execute($session);
        
        $this->assertTrue($resultSession->success);
        $this->assertNotNull($resultSession->features);
        $this->assertCount(1, $resultSession->features);
        
        $feature = $resultSession->features->all()[0];
        $this->assertEquals('life_line', $feature->feature);
        $this->assertEquals('broken', $feature->value);
        
        $this->assertArrayHasKey('timeline', $resultSession->diagnostics);
        $this->assertCount(5, $resultSession->diagnostics['timeline']);
    }
}
