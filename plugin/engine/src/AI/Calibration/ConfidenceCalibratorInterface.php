<?php

namespace AIAnalysisEngine\AI\Calibration;

use AIAnalysisEngine\AI\DTO\ProviderResponse;

interface ConfidenceCalibratorInterface
{
    /**
     * Calibrates raw provider confidences against a known dataset baseline.
     * Often data-driven (e.g. gemini.json mappings).
     */
    public function calibrate(ProviderResponse $response): ProviderResponse;
}
