<?php

namespace AIAnalysisEngine\Facade\DTO;

use AIAnalysisEngine\Presentation\DTO\ComposedReport;

class EngineResult
{
    public ComposedReport $reportModel;
    public string $html;
    public array $metadata;

    public function __construct(ComposedReport $reportModel, string $html, array $metadata = [])
    {
        $this->reportModel = $reportModel;
        $this->html = $html;
        $this->metadata = $metadata;
    }
}
