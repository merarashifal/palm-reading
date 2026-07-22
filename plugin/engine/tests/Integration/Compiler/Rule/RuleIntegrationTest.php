<?php

namespace AIAnalysisEngine\Tests\Integration\Compiler\Rule;

use AIAnalysisEngine\Knowledge\Compiler\CompiledKnowledgePack;
use AIAnalysisEngine\Knowledge\Compiler\CompilerContext;
use AIAnalysisEngine\Knowledge\Compiler\Rule\RuleCompiler;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;
use PHPUnit\Framework\TestCase;

class RuleIntegrationTest extends TestCase
{
    public function testRuleCompilerOrchestratorPipeline()
    {
        // Setup mock environment
        $manifest = [
            'entry_points' => [
                'rules/palmistry.json',
            ]
        ];
        
        $registry = $this->createMock(KnowledgeRegistry::class);
        $registry->method('loadDictionary')
            ->willReturn([
                ['rule_uid' => 'r_1', 'priority' => 10, 'feature' => 'f1'],
                ['rule_uid' => 'r_2', 'priority' => 5, 'feature' => 'f2'],
                ['rule_uid' => 'r_1', 'priority' => 20, 'feature' => 'f1_duplicate_better']
            ]);
            
        $knowledgeContext = new KnowledgeContext($manifest, [], '/fake/path', '', '', '', '');
        $pack = new CompiledKnowledgePack();
        $pack->manifest = $manifest;
        
        $context = new CompilerContext($knowledgeContext, $registry, $pack);
        
        $compiler = new RuleCompiler();
        $result = $compiler->compile($context);
        
        $this->assertTrue($result->success);
        
        // Ensure deduplication and sorting worked (r_1 with priority 20 should win)
        $this->assertCount(2, $pack->rules);
        $rules = $pack->rules->all();
        
        $this->assertEquals('r_1', $rules[0]->uid);
        $this->assertEquals(20, $rules[0]->priority);
        $this->assertEquals('f1_duplicate_better', $rules[0]->feature);
        
        $this->assertEquals('r_2', $rules[1]->uid);
        $this->assertEquals(5, $rules[1]->priority);
        
        // Assert stats
        $this->assertEquals(3, $result->statistics['rules_processed']);
        $this->assertEquals(2, $result->statistics['rules_indexed']);
    }
}
