<?php

namespace AIAnalysisEngine\Tests\Integration\Compiler;

use AIAnalysisEngine\Knowledge\Compiler\CompiledKnowledgePack;
use AIAnalysisEngine\Knowledge\Compiler\CompilerContext;
use AIAnalysisEngine\Knowledge\Compiler\KnowledgeCompiler;
use AIAnalysisEngine\Knowledge\Compiler\ManifestCompiler;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;
use PHPUnit\Framework\TestCase;

class ManifestIntegrationTest extends TestCase
{
    public function testPipelineWithManifestCompiler()
    {
        $rawManifest = [
            'name' => 'Real Pack',
            'languages' => ['en', 'hi'],
            'features' => ['fingers', 'mounts'],
            'entry_points' => ['rules/palm/fingers']
        ];

        $knowledgeContext = new KnowledgeContext($rawManifest, [], '', '', '', '', '');
        $registry = new KnowledgeRegistry();
        $pack = new CompiledKnowledgePack();
        
        $context = new CompilerContext($knowledgeContext, $registry, $pack);
        $pipeline = new KnowledgeCompiler();
        
        $pipeline->registerModule(new ManifestCompiler(), 10);
        
        $report = $pipeline->compile($context);
        
        $this->assertTrue($report->success);
        $this->assertArrayHasKey('name', $report->pack->manifest);
        $this->assertEquals('Real Pack', $report->pack->manifest['name']);
        
        // Assert the results array has the manifest module's result
        $this->assertCount(1, $report->results);
        $manifestResult = $report->results[0];
        $this->assertEquals('ManifestCompiler', $manifestResult->module);
        $this->assertEquals(2, $manifestResult->statistics['languages']);
    }
}
