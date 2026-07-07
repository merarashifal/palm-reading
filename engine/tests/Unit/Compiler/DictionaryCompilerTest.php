<?php

namespace AIAnalysisEngine\Tests\Unit\Compiler;

use AIAnalysisEngine\Knowledge\Compiler\CompiledKnowledgePack;
use AIAnalysisEngine\Knowledge\Compiler\CompilerContext;
use AIAnalysisEngine\Knowledge\Compiler\DictionaryCompiler;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;
use PHPUnit\Framework\TestCase;

class DictionaryCompilerTest extends TestCase
{
    public function testDictionaryCompilationSnapshot()
    {
        $manifest = [
            'dictionaries' => ['semantic', 'openings']
        ];
        
        $knowledgeContext = new KnowledgeContext($manifest, [], '/fake/path', '', '', '', '');
        
        // Mock registry to return specific dictionaries when getDeclaredDictionaries is called
        $registry = $this->createMock(KnowledgeRegistry::class);
        $registry->method('getDeclaredDictionaries')->willReturn([
            'semantic' => [
                'career' => ['positive' => 'Good', 'negative' => 'Bad'],
                'love' => ['positive' => 'Great']
            ],
            'openings' => [
                'general' => 'Hello',
                'specific' => 'Welcome'
            ]
        ]);
        
        $pack = new CompiledKnowledgePack();
        $pack->manifest = $manifest; // Simulate ManifestCompiler having run
        
        $context = new CompilerContext($knowledgeContext, $registry, $pack);
        $compiler = new DictionaryCompiler();
        
        $result = $compiler->compile($context);
        
        $this->assertTrue($result->success);
        
        $dictionaries = $pack->dictionaries->all();
        
        $expectedSnapshot = [
            'semantic' => [
                'career' => ['positive' => 'Good', 'negative' => 'Bad'],
                'love' => ['positive' => 'Great']
            ],
            'openings' => [
                'general' => 'Hello',
                'specific' => 'Welcome'
            ]
        ];
        
        $this->assertEquals($expectedSnapshot, $dictionaries);
        
        $this->assertEquals(2, $result->statistics['dictionary_count']);
        $this->assertEquals(5, $result->statistics['entry_count']);
        $this->assertEquals('semantic', $result->statistics['largest_dictionary']['name']);
        $this->assertEquals(3, $result->statistics['largest_dictionary']['entries']);
    }
}
