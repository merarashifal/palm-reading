<?php

namespace AIAnalysisEngine\Renderer;

class HtmlRenderer
{
    /**
     * Renders a beautiful HTML Free Report from the InferenceResult.
     * Currently expects an array structure representing the InferenceResult.json
     */
    public function renderFreeReport(array $inferenceResult): string
    {
        $score = $inferenceResult['overall_score'] ?? 89; // Mock default for MVP
        $strongestTrait = $inferenceResult['strongest_trait'] ?? 'Leadership';
        
        $careerStars = $this->renderStars($inferenceResult['scores']['career'] ?? 5);
        $relationshipStars = $this->renderStars($inferenceResult['scores']['relationships'] ?? 4);

        $detectedCount = $inferenceResult['metrics']['detected_features'] ?? 63;
        $explainedCount = $inferenceResult['metrics']['explained_insights'] ?? 11;
        $hiddenCount = $inferenceResult['metrics']['hidden_insights'] ?? 52;
        
        $rareDiscovery = "";
        if (isset($inferenceResult['rare_discoveries']) && count($inferenceResult['rare_discoveries']) > 0) {
            $rareDiscovery = "
            <div class='card rare'>
                <h3>⭐ Rare Discovery</h3>
                <p><strong>{$inferenceResult['rare_discoveries'][0]['title']}</strong></p>
                <p>Unlock to reveal explanation.</p>
            </div>";
        }

        return <<<HTML
<style>
    body { font-family: 'Inter', sans-serif; background: #0f172a; color: #f8fafc; margin: 0; padding: 20px; }
    .report-container { max-width: 600px; margin: 0 auto; }
    .header { text-align: center; margin-bottom: 30px; }
    .header h1 { font-size: 24px; font-weight: 700; background: linear-gradient(90deg, #38bdf8, #818cf8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .card { background: #1e293b; border-radius: 12px; padding: 20px; margin-bottom: 15px; border: 1px solid #334155; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
    .card h3 { font-size: 14px; text-transform: uppercase; color: #94a3b8; margin: 0 0 10px 0; letter-spacing: 0.05em; }
    .card .value { font-size: 24px; font-weight: 600; color: #f1f5f9; }
    .score-card { text-align: center; background: linear-gradient(135deg, #1e293b, #0f172a); border: 2px solid #38bdf8; }
    .score-card .value { font-size: 48px; color: #38bdf8; }
    .rare { background: linear-gradient(135deg, #4c1d95, #1e1b4b); border: 1px solid #8b5cf6; }
    .rare h3 { color: #c4b5fd; }
    .cta-card { background: #0f172a; text-align: center; border: 1px dashed #475569; }
    .cta-stats { display: flex; justify-content: space-around; margin: 20px 0; }
    .cta-stat-item span { display: block; font-size: 20px; font-weight: bold; color: #e2e8f0; }
    .cta-stat-item small { color: #64748b; font-size: 12px; }
    .btn { display: inline-block; background: #38bdf8; color: #0f172a; padding: 15px 30px; border-radius: 30px; font-weight: bold; text-decoration: none; font-size: 16px; transition: transform 0.2s; }
    .btn:hover { transform: scale(1.05); }
    .stars { color: #fbbf24; }
</style>

<div class="report-container">
    <div class="header">
        <h1>YOUR FREE REPORT</h1>
    </div>

    <div class="card score-card">
        <h3>Overall Palm Score</h3>
        <div class="value">{$score} / 100</div>
    </div>

    <div class="card">
        <h3>Your Strongest Trait</h3>
        <div class="value">{$strongestTrait}</div>
    </div>

    <div class="card">
        <h3>Career</h3>
        <div class="value stars">{$careerStars}</div>
    </div>

    <div class="card">
        <h3>Relationships</h3>
        <div class="value stars">{$relationshipStars}</div>
    </div>

    {$rareDiscovery}

    <div class="card cta-card">
        <h3>Your palm contains</h3>
        <div class="cta-stats">
            <div class="cta-stat-item"><span>{$detectedCount}</span><small>Observations</small></div>
            <div class="cta-stat-item"><span>{$explainedCount}</span><small>Insights</small></div>
            <div class="cta-stat-item"><span>{$hiddenCount}</span><small>Hidden</small></div>
        </div>
        <a href="#" class="btn">Unlock Complete Report for ₹299</a>
    </div>
</div>
HTML;
    }

    private function renderStars(int $count): string
    {
        $stars = '';
        for ($i = 0; $i < 5; $i++) {
            $stars .= ($i < $count) ? '★' : '☆';
        }
        return $stars;
    }
}
