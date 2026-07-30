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

if (!defined('PPB_PLUGIN_DIR')) {
    define('PPB_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('PPB_PLUGIN_URL')) {
    define('PPB_PLUGIN_URL', plugin_dir_url(__FILE__));
}

$missing_files = array();

$files_to_require = array(
    'AIAnalysisEngine\Metrics\MetricsService' => 'includes/Metrics/MetricsService.php',
    'AIAnalysisEngine\Config\Settings' => 'includes/Config/Settings.php',
    'AIAnalysisEngine\Admin\Dashboard' => 'includes/Admin/Dashboard.php'
);

foreach ($files_to_require as $class_name => $file_path) {
    if (!class_exists($class_name)) {
        $full_path = PPB_PLUGIN_DIR . $file_path;
        if (file_exists($full_path)) {
            require_once $full_path;
        } else {
            $missing_files[] = $file_path;
        }
    }
}

if (!empty($missing_files)) {
    add_action('admin_notices', function() use ($missing_files) {
        echo '<div class="notice notice-error"><p><strong>Personal Palm Blueprint Error:</strong> The following required files are missing. Please ensure the plugin was uploaded correctly: <br>' . implode('<br>', $missing_files) . '</p></div>';
    });
    // Stop initialization without fatal error
    return;
}

if (!class_exists('PalmReaderPlugin')) {
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
            try {
                if (method_exists('\AIAnalysisEngine\Config\Settings', 'init')) {
                    \AIAnalysisEngine\Config\Settings::init();
                }
                if (method_exists('\AIAnalysisEngine\Admin\Dashboard', 'init')) {
                    \AIAnalysisEngine\Admin\Dashboard::init();
                }
                
                register_activation_hook(__FILE__, array($this, 'activate'));
                register_deactivation_hook(__FILE__, array($this, 'deactivate'));

                add_action('init', array($this, 'addRewriteRules'));
                add_filter('query_vars', array($this, 'addQueryVars'));
                add_action('template_redirect', array($this, 'handleTemplateRedirects'));
                add_action('template_include', array($this, 'loadTemplates'));
                
                add_action('wp_enqueue_scripts', array($this, 'enqueueAssets'));

                add_action('wp_ajax_nopriv_ppb_upload', array($this, 'handleUpload'));
                add_action('wp_ajax_ppb_upload', array($this, 'handleUpload'));
                
                add_action('wp_ajax_nopriv_ppb_unlock', array($this, 'handleUnlock'));
                add_action('wp_ajax_ppb_unlock', array($this, 'handleUnlock'));

                add_action('wp_ajax_nopriv_ppb_feedback', array($this, 'handleFeedback'));
                add_action('wp_ajax_ppb_feedback', array($this, 'handleFeedback'));
            } catch (\Exception $e) {
                file_put_contents(PPB_PLUGIN_DIR . 'ppb_fatal_error.log', date('Y-m-d H:i:s') . ' Constructor Exception: ' . $e->getMessage() . "\n", FILE_APPEND);
            } catch (\Error $e) {
                file_put_contents(PPB_PLUGIN_DIR . 'ppb_fatal_error.log', date('Y-m-d H:i:s') . ' Constructor Error: ' . $e->getMessage() . "\n", FILE_APPEND);
            }
        }

        public function activate()
        {
            try {
                $this->addRewriteRules();
                flush_rewrite_rules();
            } catch (\Exception $e) {
                file_put_contents(PPB_PLUGIN_DIR . 'ppb_fatal_error.log', date('Y-m-d H:i:s') . ' Activate Exception: ' . $e->getMessage() . "\n", FILE_APPEND);
            } catch (\Error $e) {
                file_put_contents(PPB_PLUGIN_DIR . 'ppb_fatal_error.log', date('Y-m-d H:i:s') . ' Activate Error: ' . $e->getMessage() . "\n", FILE_APPEND);
            }
        }

        public function deactivate()
        {
            try {
                flush_rewrite_rules();
            } catch (\Exception $e) {
                file_put_contents(PPB_PLUGIN_DIR . 'ppb_fatal_error.log', date('Y-m-d H:i:s') . ' Deactivate Exception: ' . $e->getMessage() . "\n", FILE_APPEND);
            } catch (\Error $e) {
                file_put_contents(PPB_PLUGIN_DIR . 'ppb_fatal_error.log', date('Y-m-d H:i:s') . ' Deactivate Error: ' . $e->getMessage() . "\n", FILE_APPEND);
            }
        }

        public function addRewriteRules()
        {
            add_rewrite_rule('^upload/?$', 'index.php?ppb_page=upload', 'top');
            add_rewrite_rule('^report/([^/]+)/?$', 'index.php?ppb_page=report&ppb_report_id=$matches[1]', 'top');
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
            
            if ($ppb_page === 'share') {
                $token = sanitize_text_field(get_query_var('ppb_share_token'));
                if ($token) {
                    $upload_dir = wp_upload_dir();
                    $storageDir = $upload_dir['basedir'] . '/palm-reading/analysis';
                    $dirs = glob($storageDir . '/*' , GLOB_ONLYDIR);
                    if ($dirs) {
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
                    }
                    wp_redirect(site_url('/upload'));
                    exit;
                }
            }

            $reportId = sanitize_text_field(get_query_var('ppb_report_id'));
            if ($ppb_page === 'report' && $reportId && isset($_GET['download']) && $_GET['download'] === 'pdf') {
                if (\AIAnalysisEngine\Config\Settings::isEnabled('pdf')) {
                    $upload_dir = wp_upload_dir();
                    $storageDir = $upload_dir['basedir'] . '/palm-reading/analysis/' . $reportId;
                    $pdfPath = $storageDir . '/premium.pdf';
                    
                    $stateFile = $storageDir . '/state.json';
                    if (file_exists($stateFile)) {
                        $state = json_decode(file_get_contents($stateFile), true);
                        if (!empty($state['premium_unlocked'])) {
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
            return $template;
        }

        public function enqueueAssets()
        {
            $ppb_page = get_query_var('ppb_page');
            global $post;
            
            if ($ppb_page || (isset($post) && has_shortcode($post->post_content, 'ppb_landing'))) {
                wp_enqueue_style('ppb-styles', PPB_PLUGIN_URL . 'assets/css/journey.css', array(), '1.0.0');
                wp_enqueue_script('ppb-scripts', PPB_PLUGIN_URL . 'assets/js/journey.js', array('jquery'), '1.0.0', true);
                
                wp_localize_script('ppb-scripts', 'ppbConfig', array(
                    'ajaxUrl' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('ppb_nonce')
                ));
            }
        }

        public function renderLanding()
        {
            ob_start();
            include PPB_PLUGIN_DIR . 'components/landing.php';
            return ob_get_clean();
        }

        public function handleUpload()
        {
            wp_send_json_success(array('message' => 'Upload stub'));
        }

        public function handleUnlock()
        {
            check_ajax_referer('ppb_nonce');
            
            $reportId = isset($_POST['report_id']) ? sanitize_text_field($_POST['report_id']) : '';
            $mobile = isset($_POST['mobile']) ? sanitize_text_field($_POST['mobile']) : '';
            $dob = isset($_POST['dob']) ? sanitize_text_field($_POST['dob']) : '';
            
            if (!$reportId || !$mobile || !$dob) {
                wp_send_json_error(array('message' => 'Missing required fields.'));
            }

            $upload_dir = wp_upload_dir();
            $storagePath = $upload_dir['basedir'] . '/palm-reading';
            \AIAnalysisEngine\Metrics\MetricsService::init($storagePath);
            $visitorId = \AIAnalysisEngine\Metrics\MetricsService::getVisitorId();

            $repoPath = PPB_PLUGIN_DIR . 'engine/src/Storage/CustomerProfileRepository.php';
            if (!class_exists('AIAnalysisEngine\Storage\CustomerProfileRepository')) {
                if (file_exists($repoPath)) {
                    require_once $repoPath;
                } else {
                    wp_send_json_error(array('message' => 'System configuration error: Missing repository.'));
                }
            }
            $profileRepo = new \AIAnalysisEngine\Storage\CustomerProfileRepository($storagePath);
            $profileRepo->upsertProfile($visitorId, array(
                'mobile' => $mobile,
                'dob' => $dob
            ), $reportId);

            $unlockedReports = isset($_COOKIE['ppb_unlocked']) ? explode(',', $_COOKIE['ppb_unlocked']) : array();
            if (!in_array($reportId, $unlockedReports)) {
                $unlockedReports[] = $reportId;
                setcookie('ppb_unlocked', implode(',', $unlockedReports), time() + (86400 * 365), '/');
            }

            \AIAnalysisEngine\Metrics\MetricsService::log('profile_completed', $reportId, $visitorId, array(
                'mobile_provided' => !empty($mobile)
            ));

            $premiumPath = $storagePath . '/analysis/' . $reportId . '/premium.html';
            if (file_exists($premiumPath)) {
                $premiumHtml = file_get_contents($premiumPath);
                wp_send_json_success(array('premium_html' => $premiumHtml));
            } else {
                wp_send_json_error(array('message' => 'Premium report not found on server.'));
            }
        }

        public function handleFeedback()
        {
            wp_send_json_success(array('message' => 'Feedback stub'));
        }
    }
}

if (class_exists('PalmReaderPlugin')) {
    $ppb_plugin = PalmReaderPlugin::getInstance();
    add_shortcode('ppb_landing', array($ppb_plugin, 'renderLanding'));
}







