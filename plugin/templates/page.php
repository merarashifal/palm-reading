<?php
/**
 * Main router template for Personal Palm Blueprint states.
 */

if (!defined('ABSPATH')) {
    exit;
}

$ppb_page = get_query_var('ppb_page');
$ppb_report_id = get_query_var('ppb_report_id');

// Mock state for now
$state = $ppb_page ?: 'landing';
if ($ppb_page === 'report' && $ppb_report_id) {
    $state = 'report';
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

        case 'landing':
        default:
            include PPB_PLUGIN_DIR . 'components/landing.php';
            break;
    }
    ?>
</div>

<?php
get_footer();
