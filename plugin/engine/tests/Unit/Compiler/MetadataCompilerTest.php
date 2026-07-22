<?php

namespace AIAnalysisEngine\Tests\Unit\Compiler;

use AIAnalysisEngine\Knowledge\Compiler\MetadataCompiler;
use AIAnalysisEngine\Knowledge\Compiler\CompilerContext;
use AIAnalysisEngine\Knowledge\Compiler\CompiledKnowledgePack;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;
use PHPUnit\Framework\TestCase;

class MetadataCompilerTest extends TestCase
{
    public function testMetadataCompilerIsIdempotentAndPlatformIndependent()
    {
        $manifest = [
            'version' => '1.2.0'
        ];

        $registry = $this->createMock(KnowledgeRegistry::class);
        $knowledgeContext = new KnowledgeContext($manifest, [], __DIR__, '', '', '', '');
        $pack = new CompiledKnowledgePack();
        $pack->manifest = $manifest;
        
        $context = new CompilerContext($knowledgeContext, $registry, $pack);
        $compiler = new MetadataCompiler();

        // 1. Initial compile
        $result1 = $compiler->execute($context);
        $this->assertTrue($result1->success);
        
        $metadata = $pack->metadata;
        $this->assertArrayHasKey('build', $metadata);
        $this->assertEquals('1.2.0', $metadata['build']['knowledge_pack_version']);
        $this->assertEquals('1.0', $metadata['build']['compiler_version']);
        $this->assertArrayHasKey('compiled_at', $metadata['build']);

        // 2. Second compile (Idempotency check)
        // Store previous value to ensure it gets completely overwritten cleanly
        $pack->metadata['build']['knowledge_pack_version'] = 'dirty_data';
        
        $result2 = $compiler->execute($context);
        $this->assertTrue($result2->success);
        
        $metadata2 = $pack->metadata;
        $this->assertEquals('1.2.0', $metadata2['build']['knowledge_pack_version']); // Dirty data wiped
    }
}
