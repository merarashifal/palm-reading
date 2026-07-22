<?php

namespace AIAnalysisEngine\Tests\Unit\Compiler;

use AIAnalysisEngine\Knowledge\Compiled\CompiledDictionary;
use AIAnalysisEngine\Knowledge\Compiler\DictionaryCollection;
use AIAnalysisEngine\Knowledge\Compiler\StatisticsCompiler;
use AIAnalysisEngine\Knowledge\Compiler\CompilerContext;
use AIAnalysisEngine\Knowledge\Compiler\CompiledKnowledgePack;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;
use AIAnalysisEngine\Knowledge\Compiled\CompiledRule;
use AIAnalysisEngine\Knowledge\Compiled\CompiledRuleCollection;
use PHPUnit\Framework\TestCase;

class StatisticsCompilerTest extends TestCase
{
    public function testStatisticsCompilerIsIdempotentAndAccurate()
    {
        $manifest = [];
        $registry = $this->createMock(KnowledgeRegistry::class);
        $knowledgeContext = new KnowledgeContext($manifest, [], __DIR__, '', '', '', '');
        $pack = new CompiledKnowledgePack();
        
        // Mock Rules
        $rules = new CompiledRuleCollection();
        $rule1 = new CompiledRule();
        $rule1->feature = 'heart_line';
        $rule1->section = 'love';
        $rule1->analysis = 'palmistry';
        $rule1->language = 'en';
        $rule1->visibility = 'free';
        $rule1->translations = ['hi' => '...'];
        
        $rule2 = new CompiledRule();
        $rule2->feature = 'life_line';
        $rule2->section = 'health';
        $rule2->analysis = 'palmistry';
        $rule2->language = 'en';
        $rule2->visibility = 'premium';
        $rule2->translations = ['hi' => '...', 'es' => '...'];

        $rule3 = new CompiledRule(); // Duplicate feature/section/analysis/lang
        $rule3->feature = 'life_line';
        $rule3->section = 'health';
        $rule3->analysis = 'palmistry';
        $rule3->language = 'en';
        $rule3->visibility = 'free';
        
        $rules->add($rule1);
        $rules->add($rule2);
        $rules->add($rule3);
        $pack->rules = $rules;
        
        // Mock Dictionaries
        $pack->dictionaries = new DictionaryCollection();
        $pack->dictionaries->add('test_dict', ['item1', 'item2']);
        
        $context = new CompilerContext($knowledgeContext, $registry, $pack);
        $compiler = new StatisticsCompiler();

        // 1. Initial compile
        $result1 = $compiler->execute($context);
        $this->assertTrue($result1->success);
        
        $stats = $pack->statistics;
        $this->assertEquals(3, $stats['rules']);
        $this->assertEquals(2, $stats['features']); // heart_line, life_line
        $this->assertEquals(2, $stats['sections']); // love, health
        $this->assertEquals(1, $stats['analyses']); // palmistry
        $this->assertEquals(1, $stats['languages']); // en
        $this->assertEquals(2, $stats['visibilities']); // free, premium
        $this->assertEquals(3, $stats['translations']); // 1 + 2 + 0
        $this->assertEquals(1, $stats['dictionaries']);

        // 2. Second compile (Idempotency check)
        $pack->statistics['rules'] = 999; // dirty state
        
        $result2 = $compiler->execute($context);
        $this->assertTrue($result2->success);
        
        $stats2 = $pack->statistics;
        $this->assertEquals(3, $stats2['rules']); // properly overwritten
        $this->assertEquals(2, $stats2['features']);
    }
}
