<?php
if (!defined('ABSPATH')) exit;

register_shutdown_function(function () {
    $error = error_get_last();

    if (!$error) {
        return;
    }

    $fatal_types = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if (!in_array($error['type'], $fatal_types, true)) {
        return;
    }

    error_log('[RIP FATAL] ' . wp_json_encode($error));
});
