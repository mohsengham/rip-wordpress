<?php
if (!defined('ABSPATH')) exit;

function rip_profiles_file(): string {
    return WP_CONTENT_DIR . '/rip-profiles.json';
}

function rip_load_profiles(): array {
    $file = rip_profiles_file();
    if (!file_exists($file)) {
        return [];
    }

    $json = file_get_contents($file);
    $data = json_decode($json, true);

    return is_array($data) ? $data : [];
}

function rip_save_profile(string $route, array $safe_plugins): bool {
    $profiles = rip_load_profiles();

    $profiles[$route] = [
        'disable_plugins' => array_values(array_unique($safe_plugins)),
        'learned_at' => gmdate('c'),
        'rip_version' => defined('RIP_VERSION') ? RIP_VERSION : 'unknown',
    ];

    return (bool) file_put_contents(
        rip_profiles_file(),
        json_encode($profiles, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );
}
