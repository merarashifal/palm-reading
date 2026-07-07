<?php

namespace AIAnalysisEngine\Tests\Unit\Compiler\Rule;

use AIAnalysisEngine\Knowledge\Compiler\Rule\RuleLoader;
use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;
use PHPUnit\Framework\TestCase;

class RuleLoaderTest extends TestCase
{
    public function testLoadsRulesFromRegistry()
    {
        $manifest = [
            'entry_points' => [
                'rules/palmistry.json',
            ]
        ];
        
        $registry = $this->createMock(KnowledgeRegistry::class);
        $registry->method('loadDictionary')
            ->willReturn([
                ['rule_uid' => 'r_1'],
                ['rule_uid' => 'r_2']
            ]);
            
        $loader = new RuleLoader();
        $rules = $loader->load($registry, $manifest, '/fake/path');
        
        $this->assertCount(2, $rules);
        $this->assertEquals('r_1', $rules[0]['rule_uid']);
        $this->assertEquals('r_2', $rules[1]['rule_uid']);
    }
}
