<?php

namespace AIAnalysisEngine\Inference\Runtime;

use AIAnalysisEngine\AI\Providers\DTO\NormalizedFeatureCollection;

class ConditionEvaluator
{
    public function evaluate(array $conditions, NormalizedFeatureCollection $features): bool
    {
        $type = $conditions['type'] ?? 'AND';
        $subConditions = $conditions['conditions'] ?? [];

        if (empty($subConditions)) {
            return true;
        }

        if ($type === 'AND') {
            foreach ($subConditions as $condition) {
                if (!$this->evaluateSingle($condition, $features)) {
                    return false;
                }
            }
            return true;
        }

        if ($type === 'OR') {
            foreach ($subConditions as $condition) {
                if ($this->evaluateSingle($condition, $features)) {
                    return true;
                }
            }
            return false;
        }

        return false;
    }

    private function evaluateSingle(array $condition, NormalizedFeatureCollection $features): bool
    {
        $featureType = $condition['feature_type'] ?? null;
        $operator = $condition['operator'] ?? null;
        $attribute = $condition['attribute'] ?? null;
        $evidence = $condition['evidence'] ?? null;
        $value = $condition['value'] ?? null;

        if (!$featureType) {
            return false;
        }

        // Extremely simplified check: just verify if the feature exists in the collection.
        // A true implementation would iterate through $features->getAllFeatures() and 
        // check $feature->getType() === $featureType and $feature->getAttribute($attribute) === $value.
        
        $matchedFeatures = array_filter($features->getAllFeatures(), function($f) use ($featureType) {
            return $f->getType() === $featureType;
        });

        if ($operator === 'exists') {
            return count($matchedFeatures) > 0;
        }

        foreach ($matchedFeatures as $f) {
            if ($operator === 'equals' && $attribute) {
                $attrs = $f->getAttributes();
                if (isset($attrs[$attribute]) && $attrs[$attribute] === $value) {
                    return true;
                }
            }
            if ($operator === 'contains' && $evidence) {
                $evidences = $f->getEvidence();
                if (in_array($evidence, $evidences)) {
                    return true;
                }
            }
        }

        return false;
    }
}
