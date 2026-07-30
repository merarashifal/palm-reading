<?php

namespace AIAnalysisEngine\Inference\DTO;

use AIAnalysisEngine\Pipeline\PipelineResult;

class StageResult extends PipelineResult
{
    public int $processed = 0;
    public int $modified = 0;
    public int $discarded = 0;
    public int $memoryDiff = 0;
}
