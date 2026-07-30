<?php

namespace AIAnalysisEngine\Inference\Runtime;

use AIAnalysisEngine\Inference\DTO\Insight;
use AIAnalysisEngine\Inference\DTO\Recommendation;

class RecommendationBuilder
{
    public function buildRecommendations(array $insights): array
    {
        $recommendations = [];

        /** @var Insight $insight */
        foreach ($insights as $insight) {
            $ruleOutputs = $insight->matchedRule['outputs'] ?? [];
            foreach ($ruleOutputs as $output) {
                if ($output['type'] === 'recommendation') {
                    $recommendations[] = new Recommendation(
                        product: $output['product'] ?? 'general_report',
                        priority: $insight->matchedRule['priority'] ?? 50,
                        reason: $output['reason'] ?? $insight->title,
                        visibility: $insight->visibility
                    );
                }
            }
        }

        // Sort by priority descending
        usort($recommendations, function($a, $b) {
            return $b->priority <=> $a->priority;
        });

        return $recommendations;
    }
}
