<?php
/**
 * Main router template for Personal Palm Blueprint states.
 */

if (!defined('ABSPATH')) {
    exit;
}

$ppb_page = sanitize_text_field(get_query_var('ppb_page'));
$ppb_report_id = sanitize_text_field(get_query_var('ppb_report_id'));

$state = $ppb_page ?: 'landing';
$reportExists = false;

if ($state === 'report' && $ppb_report_id) {
    // Check if report exists in storage
    $reportDir = dirname(PPB_PLUGIN_DIR) . '/engine/storage/analysis/' . $ppb_report_id;
    if (is_dir($reportDir) && file_exists($reportDir . '/metadata.json')) {
        $reportExists = true;
        // Mock loading data for Day 1
        $reportData = json_decode(file_get_contents($reportDir . '/metadata.json'), true) ?: [];
    } else {
        $state = 'empty_state';
    }
}

get_header();
?>

<div class="ppb-container ppb-section">
    <?php
    switch ($state) {
        case 'upload':
            include PPB_PLUGIN_DIR . 'components/upload_form.php';
            include PPB_PLUGIN_DIR . 'components/processing.php';
            break;
            
        case 'report':
            include PPB_PLUGIN_DIR . 'components/report_header.php';
            include PPB_PLUGIN_DIR . 'components/trust_metrics.php';
            include PPB_PLUGIN_DIR . 'components/top_discovery.php';
            include PPB_PLUGIN_DIR . 'components/report_cards.php';
            include PPB_PLUGIN_DIR . 'components/evidence_card.php';
            include PPB_PLUGIN_DIR . 'components/unlock_cta.php';
            include PPB_PLUGIN_DIR . 'components/profile_modal.php';
            break;

        case 'empty_state':
            $statusType = 'warning';
            $statusTitle = 'Report Not Found';
            $statusMessage = 'We couldn\'t find this Personal Palm Blueprint. It may have expired or the link is incorrect.';
            $statusActionText = 'Start New Analysis';
            $statusActionUrl = site_url('/upload');
            include PPB_PLUGIN_DIR . 'components/status_card.php';
            break;

        case 'landing':
        default:
            include PPB_PLUGIN_DIR . 'components/landing.php';
            break;
    }
    ?>
</div>

<?php
get_footer();
