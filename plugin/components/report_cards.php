<?php
if (!defined('ABSPATH')) exit;

$insights = $reportData['insights'] ?? [];
$isPremium = $reportData['is_premium'] ?? false;
$freeInsights = array_slice($insights, 1, 3); // next 3 insights
?>
<div class="ppb-report-cards ppb-mb-xl ppb-flex-col ppb-gap-lg">
    
    <?php foreach ($freeInsights as $insight): ?>
    <div class="ppb-card">
        <h3 class="ppb-mb-sm"><?php echo esc_html($insight['title'] ?? 'Insight'); ?></h3>
        <p class="text-md text-muted m-0"><?php echo esc_html($insight['description'] ?? ''); ?></p>
    </div>
    <?php endforeach; ?>

    <?php if (!$isPremium): ?>
        <div class="ppb-card ppb-locked-overlay">
            <h3 class="ppb-mb-sm" style="filter: blur(4px);">Career Blueprint</h3>
            <div style="filter: blur(4px); height: 60px; background: var(--neutral-200); border-radius: var(--radius-sm);"></div>
        </div>
        <div class="ppb-card ppb-locked-overlay">
            <h3 class="ppb-mb-sm" style="filter: blur(4px);">Relationship Pattern</h3>
            <div style="filter: blur(4px); height: 60px; background: var(--neutral-200); border-radius: var(--radius-sm);"></div>
        </div>
        <div class="ppb-card ppb-locked-overlay">
            <h3 class="ppb-mb-sm" style="filter: blur(4px);">Lucky Years</h3>
            <div style="filter: blur(4px); height: 60px; background: var(--neutral-200); border-radius: var(--radius-sm);"></div>
        </div>
    <?php endif; ?>

</div>
