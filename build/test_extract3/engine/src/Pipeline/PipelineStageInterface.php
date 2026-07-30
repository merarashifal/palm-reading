<?php

namespace AIAnalysisEngine\Pipeline;

interface PipelineStageInterface
{
    public function name(): string;
    public function execute(PipelineContext $context): PipelineResult;
}
