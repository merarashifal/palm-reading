<?php

namespace AIAnalysisEngine\Renderer;

use AIAnalysisEngine\Presentation\DTO\ReportDocument;
use AIAnalysisEngine\Presentation\DTO\ReportComponent;

class HtmlRenderer
{
    public function render(ReportDocument $document): string
    {
        $html = '<div class="report-container">';
        
        foreach ($document->components as $component) {
            $html .= $this->renderComponent($component);
        }
        
        $html .= '</div>';
        
        return $this->getStyles() . $html;
    }

    private function renderComponent(ReportComponent $component): string
    {
        // For BI telemetry on the frontend
        $dataAttrs = "data-analytics-id='{$component->analytics_id}' data-importance='{$component->importance}'";
        
        $html = "<div class='section component-{$component->type} style-{$component->style}' {$dataAttrs}>";
        
        if ($component->title && $component->type !== 'Hero') {
            $icon = $component->icon ? "<span class='icon'>{$component->icon}</span> " : "";
            $html .= "<h2>{$icon}{$component->title}</h2>";
        }
        if ($component->subtitle && $component->type !== 'Hero' && $component->type !== 'CTA') {
            $html .= "<p class='subtitle'>{$component->subtitle}</p>";
        }

        switch ($component->type) {
            case 'Hero':
                $html .= "
                    <div class='hero-content'>
                        <div class='hero-greeting'>{$component->subtitle}</div>
                        <h1 class='hero-title'>{$component->title}</h1>
                        <div class='hero-stats'>We discovered <span class='gold'>{$component->data['patterns_discovered']}</span> unique patterns.</div>
                        <p class='hero-message'>{$component->data['message']}</p>
                        <div class='hero-confidence'>Confidence <span class='gold'>{$component->data['confidence']}</span></div>
                    </div>
                ";
                break;
                
            case 'Metric':
                $html .= "<div class='metric-grid'>";
                foreach ($component->data as $label => $value) {
                    $html .= "
                        <div class='metric-item'>
                            <div class='metric-label'>{$label}</div>
                            <div class='metric-value'>{$value}</div>
                        </div>
                    ";
                }
                $html .= "</div>";
                break;
                
            case 'Section':
                if ($component->layout === 'cards') {
                    $html .= "<div class='cards-container'>";
                    foreach ($component->data as $insight) {
                        $html .= "
                            <div class='card insight-card'>
                                <h3>{$insight['headline']}</h3>
                                <p class='summary'>{$insight['summary']}</p>
                                <p class='details'>{$insight['details']}</p>
                                " . (!empty($insight['advice']) ? "<div class='advice'><strong>Guidance:</strong> {$insight['advice']}</div>" : "") . "
                            </div>
                        ";
                    }
                    $html .= "</div>";
                } elseif ($component->layout === 'locked_card') {
                    $html .= "
                        <div class='locked-card'>
                            <div class='lock-overlay'>
                                <div class='lock-icon'>🔒</div>
                                <div class='lock-message'>Premium Insight</div>
                            </div>
                        </div>
                    ";
                }
                break;

            case 'Evidence':
                $html .= "
                    <div class='evidence-box'>
                        <p class='evidence-intro'>{$component->data['intro']}</p>
                        <ul class='evidence-list'>
                ";
                foreach ($component->data['items'] as $item) {
                    $html .= "<li>{$item}</li>";
                }
                $html .= "
                        </ul>
                        <p class='evidence-outro'>{$component->data['outro']}</p>
                    </div>
                ";
                break;

            case 'CTA':
                $html .= "
                    <div class='cta-box'>
                        <p class='cta-subtitle'>{$component->subtitle}</p>
                        <ul class='cta-checklist'>
                ";
                foreach ($component->data['checklist'] as $item) {
                    $html .= "<li><span class='gold'>•</span> {$item}</li>";
                }
                $html .= "
                        </ul>
                        <a href='#' class='cta-button'>{$component->data['button_text']}</a>
                    </div>
                ";
                break;
        }

        $html .= "</div>";
        return $html;
    }

    private function getStyles(): string
    {
        return <<<HTML
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600&display=swap');

    :root {
        --bg-color: #0A0A0A;
        --card-bg: #141414;
        --text-main: #FDFBF7;
        --text-muted: #A3A3A3;
        --accent-gold: #D4AF37;
        --accent-gold-dark: #AA8A2A;
        --border-color: #2A2A2A;
    }
    
    body {
        font-family: 'Inter', sans-serif;
        background: var(--bg-color);
        color: var(--text-main);
        margin: 0;
        padding: 0;
        -webkit-font-smoothing: antialiased;
        line-height: 1.6;
    }
    
    h1, h2, h3, h4, .hero-title, .hero-greeting {
        font-family: 'Cormorant Garamond', serif;
        font-weight: 600;
        margin: 0;
    }

    .report-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 60px 24px;
    }

    .section {
        margin-bottom: 70px;
    }

    .gold {
        color: var(--accent-gold);
    }

    /* Hero */
    .hero-content {
        text-align: center;
        padding-bottom: 40px;
        border-bottom: 1px solid var(--border-color);
    }
    .hero-greeting {
        font-size: 24px;
        color: var(--accent-gold);
        margin-bottom: 10px;
        font-style: italic;
    }
    .hero-title {
        font-size: 48px;
        margin-bottom: 24px;
        line-height: 1.1;
        letter-spacing: -0.5px;
    }
    .hero-stats {
        font-size: 20px;
        margin-bottom: 20px;
        font-weight: 300;
    }
    .hero-message {
        font-size: 18px;
        color: var(--text-muted);
        max-width: 400px;
        margin: 0 auto 30px auto;
    }
    .hero-confidence {
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: var(--text-muted);
    }

    /* Metric Grid (Trust Panel) */
    .section h2 {
        font-size: 32px;
        margin-bottom: 30px;
        color: var(--accent-gold);
    }
    .metric-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        background: var(--card-bg);
        padding: 30px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }
    .metric-label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }
    .metric-value {
        font-size: 20px;
        font-family: 'Cormorant Garamond', serif;
        font-weight: 600;
        color: var(--text-main);
    }

    /* Cards */
    .cards-container {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .card {
        background: var(--card-bg);
        padding: 32px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }
    .card h3 {
        font-size: 26px;
        margin-bottom: 16px;
        color: var(--text-main);
    }
    .card .summary {
        font-size: 18px;
        color: var(--accent-gold);
        margin-bottom: 16px;
        font-weight: 400;
    }
    .card .details {
        font-size: 16px;
        color: var(--text-muted);
        margin-bottom: 20px;
    }
    .card .advice {
        font-size: 15px;
        background: rgba(212, 175, 55, 0.05);
        border-left: 2px solid var(--accent-gold);
        padding: 16px;
        color: var(--text-main);
    }
    
    /* Warning Style */
    .style-warning .card .summary { color: #E8A87C; }
    .style-warning .card .advice { border-left-color: #E8A87C; background: rgba(232, 168, 124, 0.05); }
    
    /* Locked Card (Blurred UI) */
    .locked-card {
        position: relative;
        overflow: hidden;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        min-height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        padding: 40px;
    }
    
    .locked-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: repeating-linear-gradient(
            45deg,
            var(--card-bg),
            var(--card-bg) 10px,
            #1a1a1a 10px,
            #1a1a1a 20px
        );
        opacity: 0.3;
        filter: blur(4px);
        z-index: 1;
    }
    
    .lock-overlay {
        position: relative;
        z-index: 2;
        text-align: center;
        background: rgba(10, 10, 10, 0.8);
        padding: 20px 40px;
        border-radius: 30px;
        border: 1px solid var(--accent-gold);
        backdrop-filter: blur(10px);
    }
    
    .lock-icon {
        font-size: 24px;
        margin-bottom: 8px;
    }
    .lock-message {
        font-size: 16px;
        color: var(--accent-gold);
        font-weight: 600;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    /* Evidence */
    .evidence-box {
        background: var(--card-bg);
        padding: 32px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }
    .evidence-intro, .evidence-outro {
        font-size: 16px;
        color: var(--text-muted);
        font-style: italic;
    }
    .evidence-list {
        list-style: none;
        padding: 0;
        margin: 20px 0;
    }
    .evidence-list li {
        font-size: 18px;
        color: var(--text-main);
        margin-bottom: 12px;
        font-family: 'Cormorant Garamond', serif;
    }

    /* CTA */
    .cta-box {
        background: var(--card-bg);
        padding: 40px;
        border-radius: 16px;
        border: 1px solid var(--accent-gold);
        text-align: center;
    }
    .cta-subtitle {
        font-size: 18px;
        color: var(--text-muted);
        margin-bottom: 24px;
    }
    .cta-checklist {
        list-style: none;
        padding: 0;
        margin: 0 auto 40px auto;
        text-align: left;
        display: inline-block;
    }
    .cta-checklist li {
        font-size: 18px;
        margin-bottom: 16px;
        color: var(--text-main);
    }
    .cta-button {
        display: block;
        width: 100%;
        background: var(--accent-gold);
        color: #0A0A0A;
        text-decoration: none;
        padding: 20px;
        border-radius: 8px;
        font-size: 18px;
        font-weight: 600;
        transition: background 0.2s;
    }
    .cta-button:hover {
        background: var(--accent-gold-dark);
    }
</style>
HTML;
    }
}

