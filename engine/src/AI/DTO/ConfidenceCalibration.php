<?php

namespace AIAnalysisEngine\AI\DTO;

class ConfidenceCalibration
{
    public float $raw;
    public float $calibrated;
    public string $model;
    public string $version;
    public string $strategy;

    public function __construct(float $raw, float $calibrated, string $model, string $version, string $strategy)
    {
        $this->raw = $raw;
        $this->calibrated = $calibrated;
        $this->model = $model;
        $this->version = $version;
        $this->strategy = $strategy;
    }
}
