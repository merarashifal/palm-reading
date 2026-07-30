<?php

namespace AIAnalysisEngine\Pipeline;

use Psr\Log\LoggerInterface;

class Pipeline
{
    /** @var array<int, array{priority: int, stage: PipelineStageInterface}> */
    protected array $stages = [];
    protected ?LoggerInterface $logger = null;

    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function register(PipelineStageInterface $stage, int $priority = 100): void
    {
        $this->stages[] = [
            'priority' => $priority,
            'stage' => $stage
        ];
    }

    /**
     * Executes the pipeline. Can be overridden to return a specialized report.
     * @return PipelineResult[]
     */
    public function process(PipelineContext $context): array
    {
        $results = [];

        usort($this->stages, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        foreach ($this->stages as $item) {
            $stage = $item['stage'];
            
            if ($this->logger) {
                $this->logger->info("Running pipeline stage: " . $stage->name());
            }

            try {
                $result = $stage->execute($context);
                $results[] = $result;

                if (!$result->success) {
                    if ($this->logger) {
                        $this->logger->error("Pipeline stage failed: " . $stage->name());
                    }
                    break; // Stop on first failure
                }
            } catch (\Exception $e) {
                if ($this->logger) {
                    $this->logger->error("Pipeline exception in stage " . $stage->name() . ": " . $e->getMessage());
                }
                
                $result = new PipelineResult();
                $result->success = false;
                $result->module = $stage->name();
                $result->errors[] = $e->getMessage();
                $results[] = $result;
                break; // Stop on exception
            }
        }

        return $results;
    }
}
