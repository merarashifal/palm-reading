<?php

namespace AIAnalysisEngine\Knowledge\Compiler;

use AIAnalysisEngine\Pipeline\PipelineStageInterface;
use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Pipeline\PipelineResult;

interface CompilerInterface extends PipelineStageInterface
{
    public function name(): string;

    /**
     * @param CompilerContext $context
     * @return CompilerResult
     */
    public function execute(PipelineContext $context): PipelineResult;
}
