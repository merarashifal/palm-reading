<?php

namespace AIAnalysisEngine\Inference\Runtime;

use AIAnalysisEngine\AI\DTO\NormalizedFeatureCollection;
use AIAnalysisEngine\Inference\DTO\InferenceResult;

class InferenceRuntime
{
    private RuleMatcher $matcher;
    private InsightScorer $scorer;
    private RecommendationBuilder $recommendationBuilder;

    public function __construct()
    {
        $this->matcher = new RuleMatcher();
        $this->scorer = new InsightScorer();
        $this->recommendationBuilder = new RecommendationBuilder();
    }

    public function run(string $knowledgePackPath, NormalizedFeatureCollection $features, string $language = 'en'): InferenceResult
    {
        $result = new InferenceResult();

        // 1. Match Rules
        $result->insights = $this->matcher->matchRules($knowledgePackPath, $features, $language);

        // 2. Score Insights
        $result->scores = $this->scorer->scoreInsights($result->insights);

        // 3. Build Recommendations
        $result->recommendations = $this->recommendationBuilder->buildRecommendations($result->insights);

        // 4. Statistics
        $result->statistics = [
            'total_insights' => count($result->insights),
            'total_recommendations' => count($result->recommendations)
        ];

        return $result;
    }
}
