<?php

namespace AIAnalysisEngine\Presentation;

use AIAnalysisEngine\Inference\DTO\InferenceResult;
use AIAnalysisEngine\Inference\DTO\Insight;
use AIAnalysisEngine\Inference\DTO\Recommendation;
use AIAnalysisEngine\Presentation\DTO\ReportModel;

class PresentationEngine
{
    public function buildReport(InferenceResult $inferenceResult): ReportModel
    {
        $report = new ReportModel();

        // 1. Summary
        $overallScore = $inferenceResult->scores['overall_score'] ?? 50;
        
        $topTrait = 'Unknown';
        $topScore = -999;
        foreach ($inferenceResult->scores['categories'] ?? [] as $cat => $score) {
            if ($score > $topScore) {
                $topScore = $score;
                $topTrait = ucfirst($cat);
            }
        }

        $report->summary = [
            'analysis_strength' => $overallScore,
            'top_trait' => $topTrait,
        ];

        // 2. Metrics
        $report->metrics = [
            'total_insights' => $inferenceResult->statistics['total_insights'] ?? 0,
            'features_detected' => $inferenceResult->statistics['features_detected'] ?? 0
        ];

        // 3. Sections & Highlights
        $premiumCount = 0;
        /** @var Insight $insight */
        foreach ($inferenceResult->insights as $insight) {
            if ($insight->visibility !== 'free') {
                $premiumCount++;
            }

            $card = [
                'title' => $insight->title,
                'description' => $insight->visibility === 'free' ? $insight->description : '🔒 ' . ($insight->unlockReason ?? 'Unlock detailed explanation'),
                'stars' => $this->convertImportanceToStars($insight->importance),
                'visibility' => $insight->visibility,
                'category' => $insight->category
            ];

            // If it's rare, push to highlights
            if (in_array('rare', $insight->matchedRule['tags'] ?? [])) {
                $report->highlights[] = $card;
            } else {
                if (!isset($report->sections[$insight->category])) {
                    $report->sections[$insight->category] = [];
                }
                $report->sections[$insight->category][] = $card;
            }
        }

        // 4. Recommendations
        /** @var Recommendation $rec */
        foreach ($inferenceResult->recommendations as $rec) {
            $report->recommendations[] = [
                'product' => $rec->product,
                'reason' => $rec->reason,
                'visibility' => $rec->visibility
            ];
        }

        // 5. CTA
        $report->cta = [
            'locked_insights_count' => $premiumCount
        ];

        return $report;
    }

    private function convertImportanceToStars(int $importance): string
    {
        $stars = 1;
        if ($importance >= 20) $stars = 2;
        if ($importance >= 40) $stars = 3;
        if ($importance >= 60) $stars = 4;
        if ($importance >= 80) $stars = 5;
        
        $output = '';
        for ($i = 0; $i < 5; $i++) {
            $output .= ($i < $stars) ? '★' : '☆';
        }
        return $output;
    }
}
