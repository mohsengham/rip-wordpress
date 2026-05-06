<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/request.php';
require_once __DIR__ . '/profile.php';
require_once __DIR__ . '/safety.php';

if (!rip_is_rest_request() && !rip_is_binary_test_request()) {
    return;
}

$profile = rip_get_profile();
if (!$profile && !rip_is_binary_test_request()) {
    return;
}
if (!$profile) {
    $profile = ['disable_plugins' => []];
}

add_filter('option_active_plugins', function ($plugins) use ($profile) {
    if (!is_array($plugins)) {
        return $plugins;
    }
    return rip_filter_active_plugins($plugins, $profile);
}, 1);

add_filter('site_option_active_sitewide_plugins', function ($plugins) use ($profile) {
    if (!is_array($plugins)) {
        return $plugins;
    }
    return rip_filter_sitewide_plugins($plugins, $profile);
}, 1);

add_action('plugins_loaded', function () use ($profile) {
    foreach ((array) ($profile['disable_hooks'] ?? []) as $hook) {
        remove_all_actions($hook);
        remove_all_filters($hook);
    }
}, 1);

add_action('before_woocommerce_init', function () use ($profile) {
    if (empty($profile['woocommerce_slim']) || !rip_is_wc_rest_request()) {
        return;
    }

    add_filter('woocommerce_load_cart_from_session', '__return_false', 1);
    add_filter('woocommerce_session_handler', '__return_false', 1);
}, 1);

add_action('init', function () use ($profile) {
    if (empty($profile['woocommerce_slim']) || !rip_is_wc_rest_request()) {
        return;
    }

    add_filter('woocommerce_load_cart_from_session', '__return_false', 1);
}, 1);

add_action('send_headers', function () {
    $config = function_exists('rip_get_config') ? rip_get_config() : [];
    if (empty($config['debug_headers'])) {
        return;
    }

    if (headers_sent()) {
        return;
    }

    $elapsed = round((microtime(true) - RIP_START) * 1000, 2);
    header('X-RIP: active');
    header('X-RIP-Time-Ms: ' . $elapsed);
}, 100);
