<?php

namespace AIAnalysisEngine\Knowledge\Compiler\Rule;

use AIAnalysisEngine\Knowledge\Compiled\CompiledRuleCollection;

class RuleOptimizer
{
    public function optimize(CompiledRuleCollection $collection): CompiledRuleCollection
    {
        $unique = [];
        foreach ($collection->all() as $rule) {
            if (!isset($unique[$rule->uid]) || $rule->priority > $unique[$rule->uid]->priority) {
                $unique[$rule->uid] = $rule;
            }
        }
        
        $rules = array_values($unique);

        usort($rules, function ($a, $b) {
            return $b->priority <=> $a->priority;
        });

        $optimized = new CompiledRuleCollection();
        foreach ($rules as $rule) {
            $optimized->add($rule);
        }
        
        return $optimized;
    }
}
