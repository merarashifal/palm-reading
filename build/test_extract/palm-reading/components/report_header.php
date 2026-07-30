<?php
if (!defined('ABSPATH')) exit;
$userName = $reportData['metadata']['user_name'] ?? 'Seeker';
?>
<div class="ppb-report-header ppb-text-center ppb-mb-xl">
    <h1 class="ppb-mb-sm">Hello <?php echo esc_html($userName); ?></h1>
    <h2 class="text-primary">Your Palm Analysis is Ready</h2>
</div>
