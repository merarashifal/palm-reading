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
    $upload_dir = wp_upload_dir();
    $reportDir = $upload_dir['basedir'] . '/palm-reading/analysis/' . $ppb_report_id;
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
            include plugin_dir_path(dirname(__FILE__)) . 'components/upload_form.php';
            include plugin_dir_path(dirname(__FILE__)) . 'components/processing.php';
            break;
            
        case 'report':
            include plugin_dir_path(dirname(__FILE__)) . 'components/report_header.php';
            include plugin_dir_path(dirname(__FILE__)) . 'components/trust_metrics.php';
            include plugin_dir_path(dirname(__FILE__)) . 'components/top_discovery.php';
            include plugin_dir_path(dirname(__FILE__)) . 'components/report_cards.php';
            include plugin_dir_path(dirname(__FILE__)) . 'components/evidence_card.php';
            include plugin_dir_path(dirname(__FILE__)) . 'components/unlock_cta.php';
            include plugin_dir_path(dirname(__FILE__)) . 'components/profile_modal.php';
            break;

        case 'empty_state':
            $statusType = 'warning';
            $statusTitle = 'Report Not Found';
            $statusMessage = 'We couldn\'t find this Personal Palm Blueprint. It may have expired or the link is incorrect.';
            $statusActionText = 'Start New Analysis';
            $statusActionUrl = site_url('/upload');
            include plugin_dir_path(dirname(__FILE__)) . 'components/status_card.php';
            break;

        case 'landing':
        default:
            include plugin_dir_path(dirname(__FILE__)) . 'components/landing.php';
            break;
    }
    ?>
</div>

<?php
get_footer();
