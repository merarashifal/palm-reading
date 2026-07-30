<?php

namespace AIAnalysisEngine\AI\Contracts;

use AIAnalysisEngine\AI\DTO\AnalysisSession;

interface AIAdapterInterface
{
    /**
     * Executes the full AI adapter pipeline.
     */
    public function execute(AnalysisSession $session): AnalysisSession;
}
