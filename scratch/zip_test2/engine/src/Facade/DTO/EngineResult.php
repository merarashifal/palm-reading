<?php

namespace AIAnalysisEngine\Facade\DTO;

use AIAnalysisEngine\Presentation\DTO\ReportDocument;

class EngineResult
{
    public ReportDocument $reportModel;
    public string $html;
    public array $metadata;

    public function __construct(ReportDocument $reportModel, string $html, array $metadata = [])
    {
        $this->reportModel = $reportModel;
        $this->html = $html;
        $this->metadata = $metadata;
    }
}
