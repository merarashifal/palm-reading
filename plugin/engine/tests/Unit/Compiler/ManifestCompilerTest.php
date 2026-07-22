<?php

namespace AIAnalysisEngine\Tests\Unit\Compiler;

use AIAnalysisEngine\Knowledge\Compiler\CompiledKnowledgePack;
use AIAnalysisEngine\Knowledge\Compiler\CompilerContext;
use AIAnalysisEngine\Knowledge\Compiler\ManifestCompiler;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;
use PHPUnit\Framework\TestCase;

class ManifestCompilerTest extends TestCase
{
    public function testManifestNormalization()
    {
        $rawManifest = [
            'name' => 'Test Pack',
            'languages' => ['en', 'EN', 'hi', 'hi', 'Es'],
            'features' => [' Mounts ', 'lines', 'Lines', 'mounts'],
            'entry_points' => ['rules\palm\lines', 'rules/palm/lines', 'rules\palm\mounts']
        ];

        $knowledgeContext = new KnowledgeContext($rawManifest, [], '', '', '', '', '');
        $registry = new KnowledgeRegistry();
        $pack = new CompiledKnowledgePack();
        
        $context = new CompilerContext($knowledgeContext, $registry, $pack);
        $compiler = new ManifestCompiler();

        $result = $compiler->compile($context);

        $this->assertTrue($result->success);

        $manifest = $pack->manifest;

        $this->assertEquals(['en', 'es', 'hi'], $manifest['languages']);
        $this->assertEquals(['lines', 'mounts'], $manifest['features']);
        $this->assertEquals(['rules/palm/lines', 'rules/palm/mounts'], $manifest['entry_points']);
        
        $this->assertEquals(3, $result->statistics['languages']);
        $this->assertEquals(2, $result->statistics['features']);
        $this->assertEquals(2, $result->statistics['entry_points']);
    }
}
