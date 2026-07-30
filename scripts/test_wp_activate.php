<?php
define('ABSPATH', true);
function plugin_dir_path($file) { return dirname($file) . '/'; }
function plugin_dir_url($file) { return '/'; }
function register_activation_hook($file, $callback) {
    global $activation_callback;
    $activation_callback = $callback;
}
function register_deactivation_hook($file, $callback) {}
function add_action($tag, $callback, $priority=10, $accepted_args=1) {}
function add_filter($tag, $callback, $priority=10, $accepted_args=1) {}
function add_shortcode($tag, $callback) {}
function wp_create_nonce() { return 'nonce'; }
function admin_url() { return '/'; }
function add_rewrite_rule() {}
function flush_rewrite_rules() {}

require 'd:\Antarman\code\palm-reading\plugin\palm-reading.php';
global $activation_callback;
call_user_func($activation_callback);
echo 'ACTIVATE SUCCESS';

