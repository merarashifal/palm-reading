<?php

namespace AIAnalysisEngine\Renderer;

use AIAnalysisEngine\Presentation\DTO\ReportModel;

class HtmlRenderer
{
    public function render(ReportModel $report): string
    {
        $strength = $report->summary['analysis_strength'] ?? 0;
        $topTrait = $report->summary['top_trait'] ?? 'N/A';
        
        $featuresCount = $report->metrics['features_detected'] ?? 0;
        $insightsCount = $report->metrics['total_insights'] ?? 0;
        $premiumCount = $report->cta['locked_insights_count'] ?? 0;

        $sectionsHtml = '';
        foreach ($report->sections as $category => $cards) {
            $sectionsHtml .= "<div class='category-section'><h2>" . ucfirst($category) . "</h2>";
            foreach ($cards as $card) {
                $isLocked = $card['visibility'] !== 'free';
                $lockClass = $isLocked ? 'locked' : '';
                
                $sectionsHtml .= "
                <div class='card {$lockClass}'>
                    <h3>{$card['title']}</h3>
                    <div class='stars'>{$card['stars']}</div>
                    <p>{$card['description']}</p>
                </div>";
            }
            $sectionsHtml .= "</div>";
        }

        $highlightsHtml = '';
        foreach ($report->highlights as $hl) {
            $isLocked = $hl['visibility'] !== 'free';
            $lockClass = $isLocked ? 'locked' : '';
            $highlightsHtml .= "
            <div class='card rare {$lockClass}'>
                <h3>⭐ Rare Pattern Found: {$hl['title']}</h3>
                <p>{$hl['description']}</p>
            </div>";
        }

        return <<<HTML
<style>
    body { font-family: 'Inter', sans-serif; background: #0f172a; color: #f8fafc; margin: 0; padding: 20px; }
    .report-container { max-width: 600px; margin: 0 auto; }
    .header { text-align: center; margin-bottom: 30px; border-bottom: 1px solid #334155; padding-bottom: 20px;}
    .header h1 { font-size: 28px; font-weight: 700; background: linear-gradient(90deg, #38bdf8, #818cf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin: 0; }
    
    .summary-block { display: flex; justify-content: space-between; margin-bottom: 20px; }
    .score-card { flex: 1; text-align: center; background: linear-gradient(135deg, #1e293b, #0f172a); border: 1px solid #38bdf8; border-radius: 12px; padding: 15px; margin-right: 10px;}
    .score-card .value { font-size: 36px; color: #38bdf8; font-weight: bold; }
    .trait-card { flex: 1; text-align: center; background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 15px; }
    .trait-card .value { font-size: 24px; color: #f1f5f9; font-weight: bold; margin-top: 10px;}
    
    .category-section h2 { font-size: 18px; color: #94a3b8; text-transform: uppercase; border-bottom: 1px solid #334155; padding-bottom: 5px;}
    
    .card { background: #1e293b; border-radius: 12px; padding: 20px; margin-bottom: 15px; border: 1px solid #334155; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    .card h3 { font-size: 16px; margin: 0 0 5px 0; color: #e2e8f0; }
    .card p { color: #cbd5e1; font-size: 14px; line-height: 1.5; margin-bottom:0;}
    .card .stars { color: #fbbf24; margin-bottom: 10px; font-size: 18px;}
    
    .card.rare { background: linear-gradient(135deg, #4c1d95, #1e1b4b); border: 1px solid #8b5cf6; }
    .card.rare h3 { color: #c4b5fd; }
    
    .card.locked { background: #0f172a; border: 1px dashed #64748b; opacity: 0.8; }
    .card.locked h3 { color: #64748b; }
    .card.locked p { color: #94a3b8; font-style: italic; }
    
    .cta-card { background: #0f172a; text-align: center; border: 1px solid #475569; margin-top:30px;}
    .cta-stats { display: flex; justify-content: space-around; margin: 20px 0; }
    .cta-stat-item span { display: block; font-size: 20px; font-weight: bold; color: #e2e8f0; }
    .cta-stat-item small { color: #64748b; font-size: 12px; }
    .btn { display: inline-block; background: #38bdf8; color: #0f172a; padding: 15px 30px; border-radius: 30px; font-weight: bold; text-decoration: none; font-size: 16px; transition: transform 0.2s; }
    .btn:hover { transform: scale(1.05); }
    
</style>

<div class="report-container">
    <div class="header">
        <h1>AI Palm Analysis</h1>
    </div>

    <div class="summary-block">
        <div class="score-card">
            <div style="font-size:12px; color:#94a3b8; text-transform:uppercase;">Analysis Strength</div>
            <div class="value">{$strength}%</div>
            <div style="color:#fbbf24; font-size: 14px;">★★★★★</div>
        </div>
        <div class="trait-card">
            <div style="font-size:12px; color:#94a3b8; text-transform:uppercase;">Strongest Trait</div>
            <div class="value">{$topTrait}</div>
        </div>
    </div>

    {$sectionsHtml}
    {$highlightsHtml}

    <div class="card cta-card">
        <div class="cta-stats">
            <div class="cta-stat-item"><span>{$featuresCount}</span><small>Features Detected</small></div>
            <div class="cta-stat-item"><span>{$insightsCount}</span><small>Insights Generated</small></div>
        </div>
        
        <p style="color:#cbd5e1; margin-bottom:20px;">🔒 {$premiumCount} Premium Insights</p>
        <a href="#" class="btn">Unlock Complete Report</a>
    </div>
</div>
HTML;
    }
}
