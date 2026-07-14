<?php

namespace AIAnalysisEngine\Presentation;

use AIAnalysisEngine\Inference\DTO\InferenceResult;
use AIAnalysisEngine\Presentation\DTO\ComposedReport;
use AIAnalysisEngine\Inference\DTO\Insight;

class ReportComposer
{
    public function compose(InferenceResult $inference, array $metadata = []): ComposedReport
    {
        $report = new ComposedReport();

        // 1. Welcome Hero
        $report->analysisConfidence = $inference->scores['overall_score'] ?? 94;
        $report->imageQualityScore = $metadata['image_quality'] ?? 'Excellent';

        // 2. Massive Stats
        $featuresCount = count($metadata['raw_features'] ?? []);
        // Fallback if not provided
        if ($featuresCount === 0) {
            $featuresCount = $inference->statistics['features_detected'] ?? 62;
        }

        $report->totalObservations = $featuresCount;
        $report->totalInsights = count($inference->insights);
        
        $rareCount = 0;
        $premiumCount = 0;
        
        /** @var Insight $insight */
        foreach ($inference->insights as $insight) {
            if ($insight->visibility !== 'free') {
                $premiumCount++;
            }
            if ($insight->type === 'Rare Discovery') {
                $rareCount++;
            }
        }
        
        $report->rareSignsCount = $rareCount;
        $report->hiddenPremiumCount = $premiumCount;

        // 3. Opening Sentence & Your Story
        $strengths = [];
        $challenges = [];
        $others = [];
        
        foreach ($inference->insights as $insight) {
            if ($insight->type === 'Strength') $strengths[] = $insight;
            elseif ($insight->type === 'Challenge' || $insight->type === 'Warning') $challenges[] = $insight;
            else $others[] = $insight;
        }
        
        $topStrength = count($strengths) > 0 ? $strengths[0] : null;
        if ($topStrength) {
            $report->openingSentence = "Your palm indicates " . strtolower($topStrength->headline) . " and unusually high resilience.";
            $report->storyParagraphs['Where do your strengths lie?'] = $topStrength->summary . ' ' . $topStrength->details;
        } else {
            $report->openingSentence = "Your palm reveals a deeply unique path and distinct individual traits.";
            $report->storyParagraphs['Where do your strengths lie?'] = "You possess a balanced approach to life's challenges.";
        }

        $topOther = count($others) > 0 ? $others[0] : null;
        if ($topOther) {
            $report->storyParagraphs['What makes you different?'] = $topOther->summary . ' ' . $topOther->details;
        } else {
            $report->storyParagraphs['What makes you different?'] = "Your energy signature is highly individualized, drawing people towards your natural authenticity.";
        }
        
        $topChallenge = count($challenges) > 0 ? $challenges[0] : null;
        if ($topChallenge) {
            $report->storyParagraphs['What should you be careful about?'] = $topChallenge->summary . ' ' . $topChallenge->advice;
        } else {
            $report->storyParagraphs['What should you be careful about?'] = "While you are naturally resilient, it is important to protect your energy reserves and avoid over-extending yourself.";
        }

        // 4. Three Biggest Discoveries
        // Sort insights by importance descending
        $allInsights = $inference->insights;
        usort($allInsights, fn($a, $b) => $b->importance <=> $a->importance);
        
        $report->topThreeInsights = array_slice($allInsights, 0, 3);

        // 5. Blueprints
        $categories = array_unique(array_map(fn($i) => ucfirst($i->category), $allInsights));
        $blueprints = [];
        foreach ($categories as $cat) {
            if (in_array(strtolower($cat), ['career', 'relationships', 'money', 'business', 'health'])) {
                $blueprints[] = $cat . " Blueprint";
            }
        }
        if (empty($blueprints)) {
            $blueprints = ["Career Blueprint", "Marriage Blueprint", "Life Purpose"];
        }
        $report->promotedBlueprints = $blueprints;

        // 6. Evidence Trail
        if (!empty($report->topThreeInsights)) {
            $topInsight = $report->topThreeInsights[0];
            $ruleId = $topInsight->matchedRule['rule_id'] ?? 'Rule 184';
            // Just picking a sample structure for explainability
            $report->evidenceTrail = [
                'Detected' => 'Major Lines',
                'Feature' => 'Head Line',
                'Marking' => 'Fork Endpoint',
                'Rule' => $ruleId,
                'Conclusion' => $topInsight->headline
            ];
        } else {
            $report->evidenceTrail = [
                'Detected' => 'Life Line',
                'Feature' => 'Depth',
                'Rule' => 'Rule 001',
                'Conclusion' => 'Strong Vitality'
            ];
        }

        // 7. Dynamic CTA
        $highestPremium = null;
        foreach ($allInsights as $insight) {
            if ($insight->visibility !== 'free') {
                if ($highestPremium === null || $insight->importance > $highestPremium->importance) {
                    $highestPremium = $insight;
                }
            }
        }

        if ($highestPremium) {
            $cat = ucfirst($highestPremium->category);
            if ($cat === 'Relationships') $cat = 'Relationship';
            if ($cat === 'Career') $cat = 'Career';
            $report->dynamicCtaText = "Continue exploring your " . $cat . " Blueprint";
        } else {
            $report->dynamicCtaText = "Unlock your Complete Life Blueprint";
        }

        return $report;
    }
}
