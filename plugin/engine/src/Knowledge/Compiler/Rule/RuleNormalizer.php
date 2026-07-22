<?php

namespace AIAnalysisEngine\Knowledge\Compiler\Rule;

use AIAnalysisEngine\Knowledge\Compiled\CompiledRule;

class RuleNormalizer
{
    /**
     * Converts a raw JSON array into a strictly typed CompiledRule DTO.
     * This is the ONLY class that knows about the JSON field mapping.
     */
    public function normalize(array $rawRule): CompiledRule
    {
        $rule = new CompiledRule();

        // Required fields with fallbacks to empty strings to avoid type errors
        // (Validation has already guaranteed their existence in a valid pack)
        $rule->uid = $rawRule['rule_uid'] ?? '';
        $rule->language = $rawRule['language'] ?? 'en';
        $rule->analysis = $rawRule['analysis'] ?? '';
        $rule->feature = $rawRule['feature'] ?? '';
        $rule->value = $rawRule['value'] ?? '';
        $rule->section = $rawRule['section'] ?? '';
        
        // Optional fields with defaults
        $rule->visibility = $rawRule['visibility'] ?? 'free';
        $rule->confidence = isset($rawRule['confidence']) ? (float)$rawRule['confidence'] : 1.0;
        $rule->priority = isset($rawRule['priority']) ? (int)$rawRule['priority'] : 0;
        
        // Arrays
        $rule->translations = $rawRule['translations'] ?? [];
        $rule->relationships = $rawRule['relationships'] ?? [];

        return $rule;
    }
}
