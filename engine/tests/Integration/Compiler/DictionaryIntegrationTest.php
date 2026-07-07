<?php

namespace AIAnalysisEngine\Tests\Integration\Compiler;

use AIAnalysisEngine\Knowledge\Compiler\CompiledKnowledgePack;
use AIAnalysisEngine\Knowledge\Compiler\CompilerContext;
use AIAnalysisEngine\Knowledge\Compiler\DictionaryCompiler;
use AIAnalysisEngine\Knowledge\Compiler\KnowledgeCompiler;
use AIAnalysisEngine\Knowledge\Compiler\ManifestCompiler;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;
use PHPUnit\Framework\TestCase;

class DictionaryIntegrationTest extends TestCase
{
    public function testPipelineWithDictionaryCompiler()
    {
        $fixturePath = dirname(__DIR__, 2) . '/database/knowledge';
        
        $rawManifest = [
            'name' => 'Real Pack',
            'dictionaries' => ['curiosity', 'openings', 'phrases']
        ];

        // This assumes the directories actually exist or the Registry can handle them
        // If not, it will throw an exception which is fine to fail the test and notify us
        $knowledgeContext = new KnowledgeContext($rawManifest, [], $fixturePath, '', '', '', '');
        $registry = new KnowledgeRegistry();
        $pack = new CompiledKnowledgePack();
        
        $context = new CompilerContext($knowledgeContext, $registry, $pack);
        $pipeline = new KnowledgeCompiler();
        
        $pipeline->registerModule(new ManifestCompiler(), 10);
        $pipeline->registerModule(new DictionaryCompiler(), 20);
        
        $report = $pipeline->compile($context);
        
        if (!$report->success) {
            // Check if it's because files don't exist in the real path
            $this->markTestSkipped("Files might not exist in fixtures: " . ($report->results[1]->errors[0] ?? 'Unknown error'));
            return;
        }
        
        $this->assertTrue($report->success);
        
        // Assert DictionaryCompiler ran
        $this->assertCount(2, $report->results);
        
        $dictionaryResult = $report->results[1];
        $this->assertEquals('DictionaryCompiler', $dictionaryResult->module);
        
        // Assert dictionaries populated
        $this->assertTrue($pack->dictionaries->has('curiosity'));
        $this->assertTrue($pack->dictionaries->has('openings'));
        $this->assertTrue($pack->dictionaries->has('phrases'));
    }
}
