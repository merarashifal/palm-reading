<?php
/**
 * Plugin Name: Personal Palm Blueprint
 * Description: AI-powered Palm Reading lead generation engine.
 * Version: 1.0.0-beta
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PPB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PPB_PLUGIN_URL', plugin_dir_url(__FILE__));

// Require necessary files
require_once PPB_PLUGIN_DIR . 'includes/Repositories/FeedbackRepository.php';
require_once PPB_PLUGIN_DIR . 'includes/Analytics/AnalyticsService.php';
require_once PPB_PLUGIN_DIR . 'includes/Config/Settings.php';
require_once PPB_PLUGIN_DIR . 'includes/Admin/Dashboard.php';

use AIAnalysisEngine\Storage\FeedbackRepository;
use AIAnalysisEngine\Config\Settings;
use AIAnalysisEngine\Admin\Dashboard;

class PalmReaderPlugin
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new PalmReaderPlugin();
        }
        return self::$instance;
    }

    private function __construct()
    {
        // Init Config & Admin
        Settings::init();
        Dashboard::init();
        // Activation Hook
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        // Init Hooks
        add_action('init', [$this, 'addRewriteRules']);
        add_filter('query_vars', [$this, 'addQueryVars']);
        add_action('template_redirect', [$this, 'handleTemplateRedirects']);
        add_action('template_include', [$this, 'loadTemplates']);
        
        // Enqueue Assets
        add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);

        // AJAX Handlers
        add_action('wp_ajax_nopriv_ppb_upload', [$this, 'handleUpload']);
        add_action('wp_ajax_ppb_upload', [$this, 'handleUpload']);
        
        add_action('wp_ajax_nopriv_ppb_unlock', [$this, 'handleUnlock']);
        add_action('wp_ajax_ppb_unlock', [$this, 'handleUnlock']);

        add_action('wp_ajax_nopriv_ppb_feedback', [$this, 'handleFeedback']);
        add_action('wp_ajax_ppb_feedback', [$this, 'handleFeedback']);
    }

    public function activate()
    {
        $this->addRewriteRules();
        flush_rewrite_rules();

        // Create Feedback Table
        FeedbackRepository::createTable();
    }

    public function deactivate()
    {
        flush_rewrite_rules();
    }

    public function addRewriteRules()
    {
        // Upload Page
        add_rewrite_rule('^upload/?$', 'index.php?ppb_page=upload', 'top');
        
        // Report Page
        add_rewrite_rule('^report/([^/]+)/?$', 'index.php?ppb_page=report&ppb_report_id=$matches[1]', 'top');

        // Share Token Route
        add_rewrite_rule('^r/([^/]+)/?$', 'index.php?ppb_page=share&ppb_share_token=$matches[1]', 'top');
    }

    public function addQueryVars($vars)
    {
        $vars[] = 'ppb_page';
        $vars[] = 'ppb_report_id';
        $vars[] = 'ppb_share_token';
        return $vars;
    }

    public function handleTemplateRedirects()
    {
        $ppb_page = get_query_var('ppb_page');
        
        // 1. Share Token Redirect (/r/TOKEN)
        if ($ppb_page === 'share') {
            $token = sanitize_text_field(get_query_var('ppb_share_token'));
            if ($token) {
                // In a real DB we would look up the report_id by share_token
                // For file-based beta, we scan the metadata.json (or we could use a fast index)
                // For now, we mock the redirect or use grep equivalent
                $storageDir = dirname(PPB_PLUGIN_DIR) . '/engine/storage/analysis';
                $dirs = glob($storageDir . '/*' , GLOB_ONLYDIR);
                foreach ($dirs as $dir) {
                    $metaPath = $dir . '/metadata.json';
                    if (file_exists($metaPath)) {
                        $meta = json_decode(file_get_contents($metaPath), true);
                        if (isset($meta['share_token']) && $meta['share_token'] === $token) {
                            $reportId = basename($dir);
                            wp_redirect(site_url('/report/' . $reportId));
                            exit;
                        }
                    }
                }
                // If not found, redirect to upload
                wp_redirect(site_url('/upload'));
                exit;
            }
        }

        // 2. PDF Streaming (?download=pdf)
        $reportId = sanitize_text_field(get_query_var('ppb_report_id'));
        if ($ppb_page === 'report' && $reportId && isset($_GET['download']) && $_GET['download'] === 'pdf') {
            if (\AIAnalysisEngine\Config\Settings::isEnabled('pdf')) {
                $storageDir = dirname(PPB_PLUGIN_DIR) . '/engine/storage/analysis/' . $reportId;
                $pdfPath = $storageDir . '/premium.pdf';
                
                // Security check
                $stateFile = $storageDir . '/state.json';
                if (file_exists($stateFile)) {
                    $state = json_decode(file_get_contents($stateFile), true);
                    if (!empty($state['premium_unlocked'])) {
                        // Mark downloaded
                        $state['pdf_downloaded'] = true;
                        file_put_contents($stateFile, json_encode($state, JSON_PRETTY_PRINT));

                        if (file_exists($pdfPath)) {
                            header('Content-Type: application/pdf');
                            header('Content-Disposition: attachment; filename="Personal_Palm_Blueprint.pdf"');
                            header('Content-Length: ' . filesize($pdfPath));
                            readfile($pdfPath);
                            exit;
                        }
                    }
                }
                wp_die('PDF not available or locked.');
            }
        }
    }

    public function loadTemplates($template)
    {
        $ppb_page = get_query_var('ppb_page');
        
        if ($ppb_page === 'upload' || $ppb_page === 'report') {
            $custom_template = PPB_PLUGIN_DIR . 'templates/page.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        
        // Let's also hijack the home page for the landing page for beta testing if requested,
        // or they can create a normal WP page and we inject it, but the instruction said state-driven.
        // If we want `/` to be landing, we could do it here, but usually a WP page is set to front page.
        // We'll provide a shortcode `[ppb_landing]` for the home page.
        
        return $template;
    }

    public function enqueueAssets()
    {
        $ppb_page = get_query_var('ppb_page');
        global $post;
        
        if ($ppb_page || (isset($post) && has_shortcode($post->post_content, 'ppb_landing'))) {
            wp_enqueue_style('ppb-styles', PPB_PLUGIN_URL . 'assets/css/journey.css', [], '1.0.0');
            wp_enqueue_script('ppb-scripts', PPB_PLUGIN_URL . 'assets/js/journey.js', ['jquery'], '1.0.0', true);
            
            wp_localize_script('ppb-scripts', 'ppbConfig', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ppb_nonce')
            ]);
        }
    }

    // Shortcode for Landing
    public function renderLanding()
    {
        ob_start();
        include PPB_PLUGIN_DIR . 'components/landing.php';
        return ob_get_clean();
    }

    public function handleUpload()
    {
        // To be implemented
        wp_send_json_success(['message' => 'Upload stub']);
    }

    public function handleUnlock()
    {
        check_ajax_referer('ppb_nonce');
        
        $reportId = sanitize_text_field($_POST['report_id'] ?? '');
        $mobile = sanitize_text_field($_POST['mobile'] ?? '');
        $dob = sanitize_text_field($_POST['dob'] ?? '');
        
        if (!$reportId || !$mobile || !$dob) {
            wp_send_json_error(['message' => 'Missing required fields.']);
        }

        // Initialize Analytics and get Visitor ID
        $storagePath = dirname(PPB_PLUGIN_DIR) . '/engine/storage';
        \AIAnalysisEngine\Analytics\AnalyticsService::init($storagePath);
        $visitorId = \AIAnalysisEngine\Analytics\AnalyticsService::getVisitorId();

        // 1. Save Profile
        require_once dirname(PPB_PLUGIN_DIR) . '/engine/src/Storage/CustomerProfileRepository.php';
        $profileRepo = new \AIAnalysisEngine\Storage\CustomerProfileRepository($storagePath);
        $profileRepo->upsertProfile($visitorId, [
            'mobile' => $mobile,
            'dob' => $dob
        ], $reportId);

        // 2. Mark this report as unlocked in session/cookie for persistence
        // For beta, we use cookies to track unlocked reports for anonymous users
        $unlockedReports = isset($_COOKIE['ppb_unlocked']) ? explode(',', $_COOKIE['ppb_unlocked']) : [];
        if (!in_array($reportId, $unlockedReports)) {
            $unlockedReports[] = $reportId;
            setcookie('ppb_unlocked', implode(',', $unlockedReports), time() + (86400 * 365), '/');
        }

        // 3. Log Analytics Event
        \AIAnalysisEngine\Analytics\AnalyticsService::log('profile_completed', $reportId, $visitorId, [
            'mobile_provided' => !empty($mobile)
        ]);

        // 4. Retrieve Premium HTML
        $premiumPath = $storagePath . '/analysis/' . $reportId . '/premium.html';
        if (file_exists($premiumPath)) {
            $premiumHtml = file_get_contents($premiumPath);
            wp_send_json_success(['premium_html' => $premiumHtml]);
        } else {
            wp_send_json_error(['message' => 'Premium report not found on server.']);
        }
    }

    public function handleFeedback()
    {
        // To be implemented
        wp_send_json_success(['message' => 'Feedback stub']);
    }
}

// Register Shortcode
add_shortcode('ppb_landing', [PalmReaderPlugin::getInstance(), 'renderLanding']);

// Initialize
PalmReaderPlugin::getInstance();
