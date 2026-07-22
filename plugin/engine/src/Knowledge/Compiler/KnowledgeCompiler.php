<?php

namespace AIAnalysisEngine\Knowledge\Compiler;

use AIAnalysisEngine\Knowledge\Compiler\Pipeline\CompilerFactory;

class KnowledgeCompiler
{
    public function compile(CompilerContext $context): CompilationReport
    {
        $pipeline = CompilerFactory::createDefaultPipeline($context->logger);
        return $pipeline->execute($context);
    }
}
