<?php
if (!defined('ABSPATH')) exit;

// We assume the first premium insight is the top discovery for this example.
$topInsight = $reportData['insights'][0] ?? null;
if (!$topInsight) return;
?>
<div class="ppb-top-discovery ppb-text-center ppb-mb-xl" style="border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: var(--space-xl) 0;">
    <div class="text-xs text-muted ppb-mb-md" style="text-transform: uppercase; letter-spacing: 0.1em;">Your Biggest Discovery</div>
    
    <h2 class="ppb-mb-md" style="font-size: var(--font-2xl); color: var(--text); line-height: 1.3;">
        <?php echo esc_html($topInsight['title'] ?? 'Exceptional Potential Detected'); ?>
    </h2>
    
    <p class="text-lg text-muted" style="max-width: 600px; margin: 0 auto;">
        <?php echo esc_html($topInsight['description'] ?? 'Your palm reveals significant strengths waiting to be unlocked.'); ?>
    </p>
</div>
