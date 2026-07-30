<?php

namespace AIAnalysisEngine\Inference\DTO;

use AIAnalysisEngine\Knowledge\Compiled\CompiledRule;

class MatchedRule
{
    public CompiledRule $rule;
    public EvidenceCollection $evidence;
    public float $matchConfidence;
    public array $matchedFeatures;
    public bool $exactMatch;
    public MatchType $lookupStrategy;

    public function __construct(
        CompiledRule $rule,
        EvidenceCollection $evidence,
        float $matchConfidence = 1.0,
        array $matchedFeatures = [],
        bool $exactMatch = true,
        MatchType $lookupStrategy = MatchType::EXACT
    ) {
        $this->rule = $rule;
        $this->evidence = $evidence;
        $this->matchConfidence = $matchConfidence;
        $this->matchedFeatures = $matchedFeatures;
        $this->exactMatch = $exactMatch;
        $this->lookupStrategy = $lookupStrategy;
    }
}
