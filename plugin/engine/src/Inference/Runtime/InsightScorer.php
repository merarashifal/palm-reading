<?php

namespace AIAnalysisEngine\Inference\Runtime;

use AIAnalysisEngine\Inference\DTO\Insight;

class InsightScorer
{
    public function scoreInsights(array $insights): array
    {
        $scores = [];
        $totalWeight = 0;
        $totalScore = 0;

        /** @var Insight $insight */
        foreach ($insights as $insight) {
            $cat = $insight->category;
            if (!isset($scores[$cat])) {
                $scores[$cat] = 0;
            }
            
            // Extract the score impact from the raw rule definition
            $impact = $insight->matchedRule['score_impact'] ?? 0;
            $scores[$cat] += $impact;

            $totalScore += $impact;
            $totalWeight++;
        }

        // Extremely simplified "Analysis Strength" calculation
        $overallScore = $totalWeight > 0 ? min(100, max(0, 50 + $totalScore)) : 50;

        return [
            'overall_score' => $overallScore,
            'categories' => $scores
        ];
    }
}
