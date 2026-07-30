<?php

namespace AIAnalysisEngine\Knowledge\Compiler\Rule;

use AIAnalysisEngine\Knowledge\Compiled\CompiledRuleCollection;

class RuleCollectionBuilder
{
    /**
     * Iterates raw rule arrays, normalizes them, and builds the strict collection.
     */
    public function build(array $rawRules, RuleNormalizer $normalizer): CompiledRuleCollection
    {
        $collection = new CompiledRuleCollection();

        foreach ($rawRules as $rawRule) {
            $rule = $normalizer->normalize($rawRule);
            $collection->add($rule);
        }

        return $collection;
    }
}
