<?php

namespace AIAnalysisEngine\Knowledge\Generator;

use AIAnalysisEngine\Pipeline\Pipeline;
use AIAnalysisEngine\Pipeline\PipelineContext;

class GeneratorPipeline extends Pipeline
{
    /**
     * Executes the generator pipeline, which writes deployment artifacts
     * to the directories defined in the BuildContext.
     * 
     * @param BuildContext $context
     */
    public function execute(PipelineContext $context): array
    {
        /** @var BuildContext $context */
        
        if ($this->logger) {
            $this->logger->info("Starting Generator Pipeline build process...");
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
                $this->logger->error("Generator Pipeline finished with errors.");
            } else {
                $this->logger->info("Generator Pipeline finished successfully.");
            }
        }

        return $results;
    }
}
