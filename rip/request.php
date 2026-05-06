<?php
if (!defined('ABSPATH')) exit;

function rip_get_request_uri(): string {
    return $_SERVER['REQUEST_URI'] ?? '';
}

function rip_is_rest_request(): bool {
    $uri = rip_get_request_uri();
    return strpos($uri, '/wp-json/') !== false || (defined('REST_REQUEST') && REST_REQUEST);
}

function rip_is_wc_rest_request(): bool {
    return strpos(rip_get_request_uri(), '/wp-json/wc/') !== false;
}

function rip_is_binary_test_request(): bool {
    return !empty($_SERVER['HTTP_X_RIP_TEST']);
}

function rip_get_binary_test_disabled_plugins(): array {
    if (!rip_is_binary_test_request() || empty($_SERVER['HTTP_X_RIP_DISABLED'])) {
        return [];
    }

    $json = base64_decode((string) $_SERVER['HTTP_X_RIP_DISABLED'], true);
    if ($json === false) {
        return [];
    }

    $plugins = json_decode($json, true);
    if (!is_array($plugins)) {
        return [];
    }

    return array_values(array_filter($plugins, 'is_string'));
}
