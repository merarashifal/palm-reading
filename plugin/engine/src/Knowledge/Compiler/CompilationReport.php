<?php

namespace AIAnalysisEngine\Knowledge\Compiler;

class CompilationReport
{
    public CompiledKnowledgePack $pack;
    /** @var CompilerResult[] */
    public array $results = [];
    public float $executionTime = 0.0;
    public int $memoryUsage = 0;
    public bool $success = true;

    public function __construct(CompiledKnowledgePack $pack)
    {
        $this->pack = $pack;
    }
}
