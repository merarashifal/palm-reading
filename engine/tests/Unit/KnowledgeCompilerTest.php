<?php

namespace AIAnalysisEngine\Tests\Unit;

use AIAnalysisEngine\Knowledge\Compiler\CompiledKnowledgePack;
use AIAnalysisEngine\Knowledge\Compiler\CompilerContext;
use AIAnalysisEngine\Knowledge\Compiler\CompilerException;
use AIAnalysisEngine\Knowledge\Compiler\CompilerInterface;
use AIAnalysisEngine\Knowledge\Compiler\CompilerResult;
use AIAnalysisEngine\Knowledge\Compiler\KnowledgeCompiler;
use AIAnalysisEngine\Knowledge\Context\KnowledgeContext;
use AIAnalysisEngine\Knowledge\Registry\KnowledgeRegistry;
use PHPUnit\Framework\TestCase;

class KnowledgeCompilerTest extends TestCase
{
    private CompilerContext $context;

    protected function setUp(): void
    {
        $knowledgeContext = new KnowledgeContext([], [], '', '', '', '', '');
        $registry = new KnowledgeRegistry();
        $pack = new CompiledKnowledgePack();
        
        $this->context = new CompilerContext($knowledgeContext, $registry, $pack);
    }

    public function testCompilerExecutesModulesInPriorityOrder()
    {
        $module1 = $this->createMock(CompilerInterface::class);
        $module1->method('name')->willReturn('Module1');
        $module1->expects($this->once())->method('compile')->willReturnCallback(function (CompilerContext $c) {
            $c->pack->manifest['m1'] = true;
            $result = new CompilerResult();
            $result->success = true;
            return $result;
        });

        $module2 = $this->createMock(CompilerInterface::class);
        $module2->method('name')->willReturn('Module2');
        $module2->expects($this->once())->method('compile')->willReturnCallback(function (CompilerContext $c) {
            // Module 2 relies on Module 1 having run
            $this->assertTrue($c->pack->manifest['m1'] ?? false);
            $c->pack->manifest['m2'] = true;
            $result = new CompilerResult();
            $result->success = true;
            return $result;
        });

        $compiler = new KnowledgeCompiler();
        // Register out of order
        $compiler->registerModule($module2, 20);
        $compiler->registerModule($module1, 10);

        $report = $compiler->compile($this->context);

        $this->assertTrue($report->success);
        $this->assertTrue($report->pack->manifest['m1']);
        $this->assertTrue($report->pack->manifest['m2']);
    }

    public function testCompilerStopsOnFailure()
    {
        $module1 = $this->createMock(CompilerInterface::class);
        $module1->method('name')->willReturn('Module1');
        $module1->expects($this->once())->method('compile')->willReturnCallback(function () {
            $result = new CompilerResult();
            $result->success = false;
            $result->errors[] = "Failed";
            return $result;
        });

        $module2 = $this->createMock(CompilerInterface::class);
        $module2->method('name')->willReturn('Module2');
        $module2->expects($this->never())->method('compile');

        $compiler = new KnowledgeCompiler();
        $compiler->registerModule($module1, 10);
        $compiler->registerModule($module2, 20);

        $report = $compiler->compile($this->context);

        $this->assertFalse($report->success);
        $this->assertCount(1, $report->results);
    }
    
    public function testCompilerStopsOnException()
    {
        $module1 = $this->createMock(CompilerInterface::class);
        $module1->method('name')->willReturn('Module1');
        $module1->expects($this->once())->method('compile')->willThrowException(new CompilerException("Exception occurred"));

        $module2 = $this->createMock(CompilerInterface::class);
        $module2->method('name')->willReturn('Module2');
        $module2->expects($this->never())->method('compile');

        $compiler = new KnowledgeCompiler();
        $compiler->registerModule($module1, 10);
        $compiler->registerModule($module2, 20);

        $report = $compiler->compile($this->context);

        $this->assertFalse($report->success);
        $this->assertCount(1, $report->results);
        $this->assertEquals("Exception occurred", $report->results[0]->errors[0]);
    }
}
