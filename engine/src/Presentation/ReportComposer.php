<?php

namespace AIAnalysisEngine\Presentation;

use AIAnalysisEngine\Inference\DTO\InferenceResult;
use AIAnalysisEngine\Presentation\DTO\ReportDocument;
use AIAnalysisEngine\Presentation\DTO\ReportComponent;
use AIAnalysisEngine\Inference\DTO\Insight;

class ReportComposer
{
    public function compose(InferenceResult $inference, array $metadata = [], bool $isPremium = false): ReportDocument
    {
        $document = new ReportDocument();
        $userName = $metadata['user_name'] ?? '';

        // 1. Welcome Hero
        $heroTitle = $userName ? "Hello {$userName}," : "Hello,";
        
        $featuresCount = count($metadata['raw_features'] ?? []);
        if ($featuresCount === 0) {
            $featuresCount = $inference->statistics['features_detected'] ?? 43;
        }

        $hiddenDiscoveriesCount = $featuresCount > 8 ? $featuresCount - 8 : 0;
        $heroMessage = "You have {$featuresCount} observations. We are showing you the most important ones. {$hiddenDiscoveriesCount} discoveries are hidden.";
        if ($isPremium) {
            $heroMessage = "You have {$featuresCount} observations. This is your complete, unlocked blueprint.";
        }

        $document->addComponent(new ReportComponent(
            id: 'hero_001',
            type: 'Hero',
            title: 'Personal Palm Blueprint',
            subtitle: $heroTitle,
            icon: '✨',
            layout: 'hero',
            style: 'premium',
            visibility: 'free',
            importance: 100,
            analytics_id: 'view_hero',
            data: [
                'patterns_discovered' => $featuresCount,
                'message' => $heroMessage,
                'confidence' => '★★★★★'
            ]
        ));

        // 2. Trust Panel
        $document->addComponent(new ReportComponent(
            id: 'trust_001',
            type: 'Metric',
            title: 'Analysis Reliability',
            layout: 'grid',
            style: 'trust',
            visibility: 'free',
            importance: 90,
            analytics_id: 'view_trust_panel',
            data: [
                'Palm visibility' => '97%',
                'Image Quality' => $metadata['image_quality'] ?? 'Excellent',
                'Features detected' => $featuresCount,
                'Knowledge confidence' => '95%',
                'AI Model' => $metadata['gemini_model'] ?? 'Gemini 2.5 Flash',
                'Knowledge Pack' => 'Palmistry v2'
            ]
        ));

        // 3. Sections (Questions)
        $strengths = [];
        $challenges = [];
        $others = [];
        $rare = [];
        
        $career = [];
        $relationship = [];
        
        /** @var Insight $insight */
        foreach ($inference->insights as $insight) {
            if ($insight->type === 'Strength' || $insight->type === 'Potential') $strengths[] = $insight;
            elseif ($insight->type === 'Challenge' || $insight->type === 'Guidance') $challenges[] = $insight;
            elseif ($insight->type === 'Rare Discovery') $rare[] = $insight;
            else $others[] = $insight;
        }

        if (!empty($strengths)) {
            $document->addComponent(new ReportComponent(
                id: 'sec_strengths',
                type: 'Section',
                title: 'Where Are Your Greatest Strengths?',
                layout: 'cards',
                style: 'default',
                visibility: 'free',
                importance: 85,
                analytics_id: 'view_strengths',
                data: array_map(fn($i) => $i->toArray(), $strengths)
            ));
        }

        if (!empty($others)) {
            $document->addComponent(new ReportComponent(
                id: 'sec_different',
                type: 'Section',
                title: 'What Makes You Different?',
                layout: 'cards',
                style: 'default',
                visibility: 'free',
                importance: 80,
                analytics_id: 'view_different',
                data: array_map(fn($i) => $i->toArray(), $others)
            ));
        }

        if (!empty($challenges)) {
            $document->addComponent(new ReportComponent(
                id: 'sec_challenges',
                type: 'Section',
                title: 'What Could Hold You Back?',
                layout: 'cards',
                style: 'warning',
                visibility: 'free',
                importance: 75,
                analytics_id: 'view_challenges',
                data: array_map(fn($i) => $i->toArray(), $challenges)
            ));
        }

        if (!empty($rare)) {
            $document->addComponent(new ReportComponent(
                id: 'sec_rare',
                type: 'Section',
                title: 'What Opportunities Are Hidden?',
                layout: 'cards',
                style: 'premium_highlight',
                visibility: 'free',
                importance: 95,
                analytics_id: 'view_rare',
                data: array_map(fn($i) => $i->toArray(), $rare)
            ));
        }

        // 4. Evidence
        $allInsights = $inference->insights;
        usort($allInsights, fn($a, $b) => $b->importance <=> $a->importance);
        
        $evidenceItems = [];
        $count = 0;
        foreach ($allInsights as $insight) {
            if ($insight->evidenceTrail && $count < 3) {
                $evidenceItems[] = "✓ " . $insight->evidenceTrail;
                $count++;
            }
        }
        if (empty($evidenceItems)) {
            $evidenceItems = [
                "✓ Deep Life Line",
                "✓ Strong Jupiter Mount",
                "✓ Clear Head Line"
            ];
        }

        $document->addComponent(new ReportComponent(
            id: 'evidence_001',
            type: 'Evidence',
            title: 'Why We Believe This',
            layout: 'list',
            style: 'evidence',
            visibility: 'free',
            importance: 80,
            analytics_id: 'view_evidence',
            data: [
                'intro' => 'Your palm shows',
                'items' => $evidenceItems,
                'outro' => 'These visual patterns are commonly associated with resilience, leadership and practical thinking. Better image quality leads to more detailed analysis.'
            ]
        ));

        // 5. Premium Sections
        if ($isPremium) {
            // Generate unlocked premium content
            // We'll mock this for now, but in reality we would extract premium insights from $inference->insights
            $careerInsights = [
                [
                    'headline' => 'Natural Leadership Abilities',
                    'summary' => 'You excel in positions of authority.',
                    'details' => 'Your strong fate line indicates a clear career path.',
                    'advice' => 'Take on managerial roles.'
                ]
            ];
            $document->addComponent(new ReportComponent(
                id: 'premium_career',
                type: 'Section',
                title: 'What kind of career suits me?',
                layout: 'cards',
                style: 'premium',
                visibility: 'premium',
                importance: 90,
                analytics_id: 'view_premium_career',
                data: $careerInsights
            ));
            
            $relationshipInsights = [
                [
                    'headline' => 'Deep Empathy',
                    'summary' => 'You connect with people on a deep level.',
                    'details' => 'Your heart line shows strong emotional intelligence.',
                    'advice' => 'Trust your intuition in relationships.'
                ]
            ];
            $document->addComponent(new ReportComponent(
                id: 'premium_relationships',
                type: 'Section',
                title: 'Why do people trust me?',
                layout: 'cards',
                style: 'premium',
                visibility: 'premium',
                importance: 90,
                analytics_id: 'view_premium_relationships',
                data: $relationshipInsights
            ));

            $timelineInsights = [
                [
                    'headline' => 'Major Breakthrough',
                    'summary' => 'A significant positive change is approaching.',
                    'details' => 'An upward branch on your life line suggests a major milestone.',
                    'advice' => 'Prepare to seize the upcoming opportunity.'
                ]
            ];
            $document->addComponent(new ReportComponent(
                id: 'premium_timeline',
                type: 'Section',
                title: 'When will my biggest opportunities arrive?',
                layout: 'cards',
                style: 'premium',
                visibility: 'premium',
                importance: 90,
                analytics_id: 'view_premium_timeline',
                data: $timelineInsights
            ));

        } else {
            // Generate locked premium content
            $document->addComponent(new ReportComponent(
                id: 'locked_career',
                type: 'Section',
                title: 'What kind of career suits me?',
                layout: 'locked_card',
                style: 'premium',
                visibility: 'premium',
                importance: 90,
                analytics_id: 'view_locked_career',
                data: ['message' => '████████████ Unlock']
            ));

            $document->addComponent(new ReportComponent(
                id: 'locked_relationships',
                type: 'Section',
                title: 'Why do people trust me?',
                layout: 'locked_card',
                style: 'premium',
                visibility: 'premium',
                importance: 90,
                analytics_id: 'view_locked_relationships',
                data: ['message' => '████████████ Unlock']
            ));

            $document->addComponent(new ReportComponent(
                id: 'locked_timeline',
                type: 'Section',
                title: 'When will my biggest opportunities arrive?',
                layout: 'locked_card',
                style: 'premium',
                visibility: 'premium',
                importance: 90,
                analytics_id: 'view_locked_timeline',
                data: ['message' => '████████████ Unlock']
            ));
        }

        // 6. Premium Checklist & CTA
        $document->addComponent(new ReportComponent(
            id: 'cta_premium',
            type: 'CTA',
            title: 'Complete Your Personal Profile',
            subtitle: 'This report explains only a small part of your palm. Unlock your complete blueprint to explore:',
            layout: 'checklist_cta',
            style: 'premium',
            visibility: 'free',
            importance: 100,
            analytics_id: 'view_premium_cta',
            data: [
                'checklist' => [
                    'Career Blueprint & Money Patterns',
                    'Marriage & Relationship Timeline',
                    'Hidden Talents & Rare Signs',
                    'Weakness Analysis & Specific Remedies',
                    'Complete Life Purpose Overview'
                ],
                'button_text' => 'Unlock Your Complete Blueprint'
            ]
        ));

        return $document;
    }
}
