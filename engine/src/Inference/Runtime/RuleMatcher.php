<?php

namespace AIAnalysisEngine\Inference\Runtime;

use AIAnalysisEngine\AI\DTO\NormalizedFeatureCollection;
use AIAnalysisEngine\Inference\DTO\Insight;

class RuleMatcher
{
    private ConditionEvaluator $evaluator;

    public function __construct()
    {
        $this->evaluator = new ConditionEvaluator();
    }

    public function matchRules(string $knowledgePackPath, NormalizedFeatureCollection $features, string $language = 'en'): array
    {
        if (!file_exists($knowledgePackPath)) {
            throw new \Exception("Knowledge pack not found: $knowledgePackPath");
        }

        $json = file_get_contents($knowledgePackPath);
        $pack = json_decode($json, true);

        if (!$pack || !isset($pack['rules'])) {
            throw new \Exception("Invalid knowledge pack JSON");
        }

        $insights = [];

        foreach ($pack['rules'] as $rule) {
            if (isset($rule['conditions']) && $this->evaluator->evaluate($rule['conditions'], $features)) {
                
                foreach ($rule['outputs'] as $output) {
                    if ($output['type'] === 'insight') {
                        // Extract localized text
                        $langData = $output['languages'][$language] ?? $output['languages']['en'] ?? [];
                        
                        $insights[] = new Insight(
                            id: $rule['rule_id'],
                            type: $output['insight_type'] ?? 'Discovery',
                            headline: $langData['headline'] ?? '',
                            summary: $langData['summary'] ?? '',
                            details: $langData['details'] ?? '',
                            advice: $langData['advice'] ?? '',
                            cta: $langData['cta'] ?? '',
                            confidence: $rule['confidence_weight'] ?? 0.9,
                            importance: $rule['importance'] ?? 50,
                            visibility: $rule['visibility'] ?? 'free',
                            category: $rule['category'] ?? 'general',
                            matchedRule: $rule,
                            evidence: [],
                            remedies: $output['remedies'] ?? [],
                            evidenceTrail: $output['evidence_trail'] ?? '',
                            unlockReason: $output['unlock_reason'] ?? null
                        );
                    }
                }
            }
        }

        return $insights;
    }
}
