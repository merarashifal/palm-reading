<?php

namespace AIAnalysisEngine\AI\Normalization;

use AIAnalysisEngine\AI\DTO\ProviderResponse;
use AIAnalysisEngine\AI\DTO\NormalizedFeatureCollection;

interface FeatureNormalizerInterface
{
    /**
     * Statelessly maps a provider-specific response to standard Engine features.
     * This is where domain translation occurs (e.g. "line_broken" -> "life_line = broken").
     */
    public function normalize(ProviderResponse $response): NormalizedFeatureCollection;
}
