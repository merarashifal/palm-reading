<?php
if (!defined('ABSPATH')) exit;
$evidenceList = $reportData['metadata']['raw_features'] ?? ['Strong Life Line', 'Deep Head Line', 'Well Developed Jupiter Mount'];
// Just take the first 3 for the evidence block
$evidenceList = array_slice($evidenceList, 0, 3);
?>
<div class="ppb-evidence-section ppb-mb-xl">
    <h3 class="ppb-mb-md">Why we believe this</h3>
    <div class="ppb-flex-col ppb-gap-sm">
        <?php foreach ($evidenceList as $evidence): ?>
        <div class="ppb-evidence-item ppb-flex-center" style="justify-content: flex-start;">
            <span class="text-success" style="margin-right: 12px; font-size: var(--font-lg);">✓</span>
            <span class="text-md" style="font-weight: 500;"><?php echo esc_html($evidence); ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>
