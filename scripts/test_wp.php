<?php
define('ABSPATH', true);
function plugin_dir_path($file) { return dirname($file) . '/'; }
function plugin_dir_url($file) { return '/'; }
function register_activation_hook($file, $callback) {}
function register_deactivation_hook($file, $callback) {}
function add_action($tag, $callback, $priority=10, $accepted_args=1) {}
function add_filter($tag, $callback, $priority=10, $accepted_args=1) {}
function add_shortcode($tag, $callback) {}
function wp_create_nonce() { return 'nonce'; }
function admin_url() { return '/'; }
require 'd:\Antarman\code\palm-reading\plugin\palm-reading.php';
echo 'SUCCESS';

