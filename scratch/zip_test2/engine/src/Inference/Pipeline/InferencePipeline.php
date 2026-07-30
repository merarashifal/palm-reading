<?php

namespace AIAnalysisEngine\Inference\Pipeline;

use AIAnalysisEngine\Pipeline\Pipeline;
use AIAnalysisEngine\Pipeline\PipelineContext;
use AIAnalysisEngine\Inference\InferenceContext;

class InferencePipeline extends Pipeline
{
    /**
     * Executes the Inference Pipeline.
     * 
     * @param InferenceContext $context
     */
    public function execute(PipelineContext $context): array
    {
        /** @var InferenceContext $context */
        
        if ($this->logger) {
            $this->logger->info("Starting Inference Pipeline execution...");
        }

        $results = parent::process($context);

        if ($this->logger) {
            $hasFailure = false;
            foreach ($results as $result) {
                if (!$result->success) {
                    $hasFailure = true;
                    break;
                }
            }
            if ($hasFailure) {
                $this->logger->error("Inference Pipeline finished with errors.");
            } else {
                $this->logger->info("Inference Pipeline finished successfully.");
            }
        }

        return $results;
    }
}
