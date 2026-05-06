<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/storage.php';

function rip_get_profile(): ?array {
    $uri = rip_get_request_uri();
    $config = rip_get_config();

    foreach (($config['routes'] ?? []) as $route => $profile) {
        if (strpos($uri, $route) !== false) {
            return is_array($profile) ? $profile : null;
        }
    }

    return null;
}

function rip_get_learned_profile_for_request(): ?array {
    $uri = rip_get_request_uri();
    $profiles = rip_load_profiles();

    foreach ($profiles as $route => $profile) {
        if (strpos($uri, $route) !== false && is_array($profile)) {
            return $profile;
        }
    }

    return null;
}

function rip_filter_active_plugins(array $plugins, array $profile): array {
    $binary_test_plugins = rip_get_binary_test_disabled_plugins();
    if (!empty($binary_test_plugins)) {
        return array_values(array_diff($plugins, $binary_test_plugins));
    }

    $learned = rip_get_learned_profile_for_request();
    if ($learned && !empty($learned['disable_plugins'])) {
        return array_values(array_diff($plugins, (array) $learned['disable_plugins']));
    }

    return array_values(array_diff($plugins, (array) ($profile['disable_plugins'] ?? [])));
}

function rip_filter_sitewide_plugins(array $plugins, array $profile): array {
    $disabled = rip_get_binary_test_disabled_plugins();

    if (empty($disabled)) {
        $learned = rip_get_learned_profile_for_request();
        $disabled = $learned['disable_plugins'] ?? ($profile['disable_plugins'] ?? []);
    }

    foreach ((array) $disabled as $plugin_file) {
        if (isset($plugins[$plugin_file])) {
            unset($plugins[$plugin_file]);
        }
    }

    return $plugins;
}
