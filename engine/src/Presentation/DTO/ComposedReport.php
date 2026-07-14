<?php

namespace AIAnalysisEngine\Presentation\DTO;

class ComposedReport
{
    // Section 1: Welcome Hero
    public int $analysisConfidence;
    public string $imageQualityScore;
    public string $openingSentence;

    // Section 2: What We Found (Massive Stats)
    public int $totalObservations;
    public int $totalInsights;
    public int $rareSignsCount;
    public int $hiddenPremiumCount;

    // Section 3: Your Story
    public array $storyParagraphs = []; // ['What makes you different?' => '...', 'Where do your strengths lie?' => '...', 'What should you be careful about?' => '...']

    // Section 4: Three Biggest Discoveries
    public array $topThreeInsights = []; // Array of Insight objects or arrays

    // Section 5: Blueprints (Premium teasing)
    public array $promotedBlueprints = []; 

    // Section 6: Why We Believe This (Evidence Trail)
    public array $evidenceTrail = [];

    // Main CTA
    public string $dynamicCtaText;
}
