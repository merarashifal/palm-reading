<?php

namespace AIAnalysisEngine\Tests\Integration;

use AIAnalysisEngine\Knowledge\Compiler\CompiledKnowledgePack;
use AIAnalysisEngine\Knowledge\Compiler\CompilerContext;
use AIAnalysisEngine\Knowledge\Compiler\CompilerInterface;
use AIAnalysisEngine\Knowledge\Compiler\CompilerResult;
use AIAnalysisEngine\Knowledge\Compiler\KnowledgeCompiler;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;
use PHPUnit\Framework\TestCase;

class DummyModuleA implements CompilerInterface
{
    public function name(): string { return 'DummyA'; }
    public function compile(CompilerContext $context): CompilerResult
    {
        $context->pack->metadata['module_a'] = true;
        $result = new CompilerResult();
        $result->module = $this->name();
        return $result;
    }
}

class DummyModuleB implements CompilerInterface
{
    public function name(): string { return 'DummyB'; }
    public function compile(CompilerContext $context): CompilerResult
    {
        $context->pack->metadata['module_b'] = true;
        $result = new CompilerResult();
        $result->module = $this->name();
        return $result;
    }
}

class DummyModuleC implements CompilerInterface
{
    public function name(): string { return 'DummyC'; }
    public function compile(CompilerContext $context): CompilerResult
    {
        $context->pack->metadata['module_c'] = true;
        $result = new CompilerResult();
        $result->module = $this->name();
        return $result;
    }
}

class CompilerIntegrationTest extends TestCase
{
    public function testFullCompilerPipeline()
    {
        $knowledgeContext = new KnowledgeContext([], [], '', '', '', '', '');
        $registry = new KnowledgeRegistry();
        $pack = new CompiledKnowledgePack();
        
        $context = new CompilerContext($knowledgeContext, $registry, $pack);
        $compiler = new KnowledgeCompiler();
        
        // Register modules
        $compiler->registerModule(new DummyModuleA(), 10);
        $compiler->registerModule(new DummyModuleB(), 20);
        $compiler->registerModule(new DummyModuleC(), 30);
        
        $report = $compiler->compile($context);
        
        $this->assertTrue($report->success);
        $this->assertCount(3, $report->results);
        
        // Assert all changes are contained in the pack
        $this->assertTrue($report->pack->metadata['module_a']);
        $this->assertTrue($report->pack->metadata['module_b']);
        $this->assertTrue($report->pack->metadata['module_c']);
    }
}
