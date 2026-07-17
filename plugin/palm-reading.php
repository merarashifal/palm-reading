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

use AIAnalysisEngine\Storage\FeedbackRepository;

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
        // Activation Hook
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        // Init Hooks
        add_action('init', [$this, 'addRewriteRules']);
        add_filter('query_vars', [$this, 'addQueryVars']);
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
    }

    public function addQueryVars($vars)
    {
        $vars[] = 'ppb_page';
        $vars[] = 'ppb_report_id';
        return $vars;
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
        // To be implemented
        wp_send_json_success(['message' => 'Unlock stub']);
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
