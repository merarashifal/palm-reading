<?php

namespace AIAnalysisEngine\Renderer;

use AIAnalysisEngine\Presentation\DTO\ComposedReport;

class HtmlRenderer
{
    public function render(ComposedReport $report): string
    {
        // 1. Welcome Hero
        $welcomeHero = "
            <div class='section hero'>
                <h1>Your Palm Analysis</h1>
                <div class='confidence-block'>
                    <div class='label'>Analysis Confidence</div>
                    <div class='score'>{$report->analysisConfidence}%</div>
                    <div class='stars'>★★★★★</div>
                </div>
                <div class='quality-block'>
                    <div class='label'>Image Quality</div>
                    <div class='quality-score'>{$report->imageQualityScore}</div>
                </div>
                <div class='success-msg'>Analysis completed successfully</div>
            </div>
        ";

        // 2. What We Found
        $stats = "
            <div class='section stats'>
                <div class='stat-row'>
                    <div class='stat-num'>{$report->totalObservations}</div>
                    <div class='stat-label'>Observations</div>
                </div>
                <div class='stat-row'>
                    <div class='stat-num'>{$report->totalInsights}</div>
                    <div class='stat-label'>Insights</div>
                </div>
                <div class='stat-row'>
                    <div class='stat-num'>{$report->rareSignsCount}</div>
                    <div class='stat-label'>Rare Signs</div>
                </div>
                <div class='stat-row'>
                    <div class='stat-num premium'>{$report->hiddenPremiumCount}</div>
                    <div class='stat-label premium-label'>Hidden Premium Insights</div>
                </div>
            </div>
        ";

        // 3. Your Story
        $storyHtml = "<div class='section story'>";
        foreach ($report->storyParagraphs as $question => $answer) {
            $storyHtml .= "
                <div class='story-block'>
                    <h2>{$question}</h2>
                    <p>{$answer}</p>
                </div>
            ";
        }
        $storyHtml .= "</div>";

        // 4. Three Biggest Discoveries
        $discoveriesHtml = "<div class='section discoveries'>";
        foreach ($report->topThreeInsights as $insight) {
            $typeIcon = '⭐';
            if ($insight->type === 'Challenge' || $insight->type === 'Warning') $typeIcon = '⚠';
            if ($insight->type === 'Rare Discovery') $typeIcon = '💎';
            
            $discoveriesHtml .= "
                <div class='discovery-card'>
                    <div class='discovery-type'>{$typeIcon} {$insight->type}</div>
                    <h3>{$insight->headline}</h3>
                    <p>{$insight->summary}</p>
                    <p class='details'>{$insight->details}</p>
                </div>
            ";
        }
        $discoveriesHtml .= "</div>";

        // 5. Blueprints
        $blueprintsHtml = "<div class='section blueprints'>
            <h2>Continue Your Analysis</h2>
            <div class='blueprint-list'>";
        foreach ($report->promotedBlueprints as $bp) {
            $blueprintsHtml .= "<div class='blueprint-item'>{$bp} <span>→</span></div>";
        }
        $blueprintsHtml .= "</div></div>";

        // 6. Evidence Trail
        $evidenceHtml = "<div class='section evidence'>
            <h2>Why did we say this?</h2>
            <div class='trail'>";
        $first = true;
        foreach ($report->evidenceTrail as $key => $val) {
            if (!$first) {
                $evidenceHtml .= "<div class='arrow'>↓</div>";
            }
            $evidenceHtml .= "<div class='trail-node'><strong>{$val}</strong></div>";
            $first = false;
        }
        $evidenceHtml .= "</div></div>";

        // 7. Final Message
        $finalMessage = "
            <div class='section final-message'>
                <h2>Every palm tells a story.</h2>
                <p>We've only shown you the beginning.</p>
                <a href='#' class='cta-button'>{$report->dynamicCtaText}</a>
            </div>
        ";

        return <<<HTML
<style>
    :root {
        --bg: #000000;
        --card-bg: #111111;
        --text: #ffffff;
        --text-muted: #888888;
        --accent: #ffffff;
        --premium: #c4b5fd;
    }
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Inter', sans-serif;
        background: var(--bg);
        color: var(--text);
        margin: 0;
        padding: 0;
        -webkit-font-smoothing: antialiased;
    }
    .report-container {
        max-width: 500px;
        margin: 0 auto;
        padding: 40px 20px;
    }
    .section {
        margin-bottom: 60px;
        border-bottom: 1px solid #222;
        padding-bottom: 60px;
    }
    .section:last-child {
        border-bottom: none;
    }
    
    /* Hero */
    .hero { text-align: center; }
    .hero h1 { font-size: 32px; font-weight: 800; letter-spacing: -1px; margin-bottom: 40px; }
    .confidence-block { margin-bottom: 30px; }
    .confidence-block .label, .quality-block .label { font-size: 14px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px; }
    .confidence-block .score { font-size: 72px; font-weight: 800; line-height: 1; margin-bottom: 10px; }
    .confidence-block .stars { color: var(--text); font-size: 20px; }
    .quality-block .quality-score { font-size: 24px; font-weight: 600; color: #4ade80; }
    .success-msg { margin-top: 30px; font-size: 14px; color: var(--text-muted); }

    /* Stats */
    .stat-row { margin-bottom: 30px; text-align: left; }
    .stat-num { font-size: 64px; font-weight: 800; line-height: 1; margin-bottom: 5px; }
    .stat-label { font-size: 20px; color: var(--text-muted); font-weight: 500; }
    .stat-num.premium { color: var(--premium); }
    .stat-label.premium-label { color: var(--premium); }

    /* Story */
    .story-block { margin-bottom: 40px; }
    .story-block h2 { font-size: 24px; font-weight: 700; margin-bottom: 15px; color: var(--text); letter-spacing: -0.5px; }
    .story-block p { font-size: 18px; line-height: 1.6; color: #cccccc; margin: 0; }

    /* Discoveries */
    .discovery-card { background: var(--card-bg); border-radius: 20px; padding: 30px; margin-bottom: 20px; border: 1px solid #222; }
    .discovery-type { font-size: 14px; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 15px; font-weight: 600; }
    .discovery-card h3 { font-size: 28px; font-weight: 700; margin: 0 0 15px 0; letter-spacing: -0.5px; }
    .discovery-card p { font-size: 16px; line-height: 1.5; color: #bbbbbb; margin: 0 0 15px 0; }
    .discovery-card .details { color: #888888; font-size: 14px; }

    /* Blueprints */
    .blueprints h2 { font-size: 28px; font-weight: 700; margin-bottom: 30px; }
    .blueprint-item { background: var(--card-bg); padding: 20px 25px; border-radius: 16px; margin-bottom: 15px; font-size: 18px; font-weight: 600; display: flex; justify-content: space-between; align-items: center; border: 1px solid #222; cursor: pointer; transition: background 0.2s; }
    .blueprint-item:hover { background: #1a1a1a; }
    .blueprint-item span { color: var(--text-muted); }

    /* Evidence */
    .evidence h2 { font-size: 20px; font-weight: 600; margin-bottom: 30px; text-align: center; color: var(--text-muted); }
    .trail { text-align: center; }
    .trail-node { display: inline-block; padding: 10px 20px; background: var(--card-bg); border-radius: 8px; font-size: 16px; border: 1px solid #333; }
    .arrow { margin: 15px 0; color: #555; font-size: 20px; }

    /* Final CTA */
    .final-message { text-align: center; padding-top: 20px; }
    .final-message h2 { font-size: 32px; font-weight: 800; margin: 0 0 10px 0; letter-spacing: -1px; }
    .final-message p { font-size: 18px; color: var(--text-muted); margin: 0 0 40px 0; }
    .cta-button { display: block; width: 100%; box-sizing: border-box; background: #ffffff; color: #000000; padding: 20px; border-radius: 30px; text-decoration: none; font-size: 18px; font-weight: 700; transition: transform 0.2s; }
    .cta-button:active { transform: scale(0.98); }
</style>

<div class="report-container">
    {$welcomeHero}
    {$stats}
    {$storyHtml}
    {$discoveriesHtml}
    {$blueprintsHtml}
    {$evidenceHtml}
    {$finalMessage}
</div>
HTML;
    }
}
