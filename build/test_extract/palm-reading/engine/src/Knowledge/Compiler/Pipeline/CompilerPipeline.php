<?php

namespace AIAnalysisEngine\Knowledge\Compiler\Pipeline;

use AIAnalysisEngine\Knowledge\Compiler\CompilerContext;
use AIAnalysisEngine\Knowledge\Compiler\CompilationReport;
use AIAnalysisEngine\Pipeline\Pipeline;
use AIAnalysisEngine\Pipeline\PipelineContext;

class CompilerPipeline extends Pipeline
{
    /**
     * Executes the compiler pipeline and builds a structured CompilationReport.
     * 
     * @param CompilerContext $context
     */
    public function execute(PipelineContext $context): CompilationReport
    {
        /** @var CompilerContext $context */
        $report = new CompilationReport($context->pack);
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Run the generic pipeline execution which returns PipelineResult[]
        $results = parent::process($context);

        // Cast and assign results
        foreach ($results as $result) {
            $report->results[] = $result;
            if (!$result->success) {
                $report->success = false;
            }
        }

        $report->executionTime = microtime(true) - $startTime;
        $report->memoryUsage = memory_get_usage() - $startMemory;

        return $report;
    }
}
