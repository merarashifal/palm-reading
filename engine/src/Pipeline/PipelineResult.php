<?php

namespace AIAnalysisEngine\Pipeline;

class PipelineResult
{
    public bool $success = false;
    public string $module = '';
    public array $errors = [];
    public array $warnings = [];
    public float $executionTime = 0.0;
    public int $memoryUsage = 0;
    public array $statistics = [];
}
