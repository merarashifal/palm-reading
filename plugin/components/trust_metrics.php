<?php
if (!defined('ABSPATH')) exit;
$confidence = $reportData['metadata']['confidence'] ?? 94;
$quality = $reportData['metadata']['image_quality'] ?? 'Excellent';
$features = $reportData['metadata']['features_detected'] ?? 47;
?>
<div class="ppb-trust-metrics ppb-flex-center ppb-gap-md ppb-mb-xl" style="flex-wrap: wrap;">
    <div class="ppb-trust-metric">
        <div class="value"><?php echo esc_html($confidence); ?>%</div>
        <div class="label">Confidence</div>
    </div>
    <div class="ppb-trust-metric">
        <div class="value"><?php echo esc_html($quality); ?></div>
        <div class="label">Image Quality</div>
    </div>
    <div class="ppb-trust-metric">
        <div class="value"><?php echo esc_html($features); ?></div>
        <div class="label">Features Found</div>
    </div>
</div>
