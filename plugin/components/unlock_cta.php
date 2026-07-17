<?php
if (!defined('ABSPATH')) exit;

$totalObservations = $reportData['metadata']['features_detected'] ?? 34;
$unlocked = 8;
$hidden = $totalObservations - $unlocked;
?>
<div class="ppb-unlock-cta ppb-card ppb-text-center ppb-mb-xl" style="background-color: var(--primary-light); border-color: var(--primary);">
    <div class="ppb-curiosity-gap ppb-mb-lg">
        <p class="text-md ppb-mb-sm">Your Personal Palm Blueprint contains <strong><?php echo esc_html($totalObservations); ?> observations</strong>.</p>
        <p class="text-md ppb-mb-sm text-success">You've unlocked <strong><?php echo esc_html($unlocked); ?></strong>.</p>
        <p class="text-md text-primary" style="font-weight: 600;">More insights waiting: <?php echo esc_html($hidden); ?></p>
    </div>

    <h2 class="ppb-mb-md">Complete Your Personal Profile</h2>
    <p class="text-muted ppb-mb-lg">Unlock your complete Personal Palm Blueprint and download your personalized PDF report.</p>
    
    <!-- This button triggers the in-page profile modal (Day 2) -->
    <button id="ppb-trigger-unlock" class="ppb-btn ppb-btn-primary">Unlock Your Personal Palm Blueprint</button>
</div>
