<?php

namespace AIAnalysisEngine\Inference\DTO;

class EngineConfiguration
{
    public string $locale;
    public string $currency;
    public string $units;
    public string $calendar;
    public string $language;
    public string $timezone;
    public float $minimumAiConfidence;
    public float $minimumRuleConfidence;
    public bool $allowExperimental;
    public int $maxCandidates;
    public bool $debug;

    public function __construct(
        string $locale = 'en_US',
        string $currency = 'USD',
        string $units = 'metric',
        string $calendar = 'gregorian',
        string $language = 'en',
        string $timezone = 'UTC',
        float $minimumAiConfidence = 0.5,
        float $minimumRuleConfidence = 0.5,
        bool $allowExperimental = false,
        int $maxCandidates = 1000,
        bool $debug = false
    ) {
        $this->locale = $locale;
        $this->currency = $currency;
        $this->units = $units;
        $this->calendar = $calendar;
        $this->language = $language;
        $this->timezone = $timezone;
        $this->minimumAiConfidence = $minimumAiConfidence;
        $this->minimumRuleConfidence = $minimumRuleConfidence;
        $this->allowExperimental = $allowExperimental;
        $this->maxCandidates = $maxCandidates;
        $this->debug = $debug;
    }
}
