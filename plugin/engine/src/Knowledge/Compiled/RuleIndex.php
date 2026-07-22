<?php

namespace AIAnalysisEngine\Knowledge\Compiled;

class RuleIndex
{
    private array $byUid = [];
    private array $byFeature = [];
    private array $byFeatureValue = [];
    private array $byAnalysisFeatureValue = [];
    private array $bySection = [];
    private array $byVisibility = [];

    /**
     * Called exclusively by CompiledRuleCollection to index a new rule.
     */
    public function indexRule(CompiledRule $rule): void
    {
        $this->byUid[$rule->uid] = $rule;
        
        $this->byFeature[$rule->feature][] = $rule;
        
        $featureValueKey = $rule->feature . '::' . $rule->value;
        $this->byFeatureValue[$featureValueKey][] = $rule;
        
        $afvKey = $rule->analysis . '::' . $rule->feature . '::' . $rule->value;
        $this->byAnalysisFeatureValue[$afvKey][] = $rule;
        
        $this->bySection[$rule->section][] = $rule;
        
        $this->byVisibility[$rule->visibility][] = $rule;
    }

    public function getByUid(string $uid): ?CompiledRule
    {
        return $this->byUid[$uid] ?? null;
    }

    public function getByFeature(string $feature): iterable
    {
        return $this->byFeature[$feature] ?? [];
    }

    public function getByFeatureValue(string $feature, string $value): iterable
    {
        return $this->byFeatureValue[$feature . '::' . $value] ?? [];
    }

    public function getByAnalysisFeatureValue(string $analysis, string $feature, string $value): iterable
    {
        return $this->byAnalysisFeatureValue[$analysis . '::' . $feature . '::' . $value] ?? [];
    }

    public function getBySection(string $section): iterable
    {
        return $this->bySection[$section] ?? [];
    }

    public function getByVisibility(string $visibility): iterable
    {
        return $this->byVisibility[$visibility] ?? [];
    }

    /**
     * Finds rules matching an Evidence object.
     * Hides the internal index structure from the Inference engine.
     */
    public function match(\AIAnalysisEngine\Inference\DTO\Evidence $evidence): CompiledRuleCollection
    {
        $collection = new CompiledRuleCollection();
        $rules = $this->getByFeatureValue($evidence->feature, $evidence->value);
        foreach ($rules as $rule) {
            $collection->add($rule);
        }
        return $collection;
    }

    /**
     * Finds rules matching a raw Feature object.
     */
    public function find(\AIAnalysisEngine\Inference\DTO\Feature $feature): CompiledRuleCollection
    {
        $collection = new CompiledRuleCollection();
        $rules = $this->getByAnalysisFeatureValue($feature->analysis, $feature->feature, $feature->value);
        foreach ($rules as $rule) {
            $collection->add($rule);
        }
        return $collection;
    }
}
