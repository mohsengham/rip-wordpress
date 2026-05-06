<?php
/**
 * Plugin Name: RIP Bootstrap
 * Description: REST API lightweight execution controller for WordPress/WooCommerce.
 * Version: 0.1.0
 * Author: RIP
 */

if (!defined('ABSPATH')) {
    exit;
}

define('RIP_VERSION', '0.1.0');
define('RIP_DIR', __DIR__ . '/rip');
define('RIP_START', microtime(true));

// WP-CLI mode: register learning command only.
if (defined('WP_CLI') && WP_CLI) {
    require_once RIP_DIR . '/cli-binary.php';
    return;
}

// Manual emergency bypass: add ?rip_safe=1 to any URL.
if (isset($_GET['rip_safe'])) {
    return;
}

// Never optimize wp-admin requests.
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($request_uri, '/wp-admin') !== false) {
    return;
}

require_once RIP_DIR . '/engine.php';
