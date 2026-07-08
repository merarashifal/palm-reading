<?php

namespace AIAnalysisEngine\Facade\DTO;

use AIAnalysisEngine\Presentation\DTO\ReportModel;

class EngineResult
{
    public ReportModel $reportModel;
    public string $html;
    public array $metadata;

    public function __construct(ReportModel $reportModel, string $html, array $metadata = [])
    {
        $this->reportModel = $reportModel;
        $this->html = $html;
        $this->metadata = $metadata;
    }
}
