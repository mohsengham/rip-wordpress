<?php
if (!defined('ABSPATH')) exit;

function rip_get_config(): array {
    return [
        'debug_headers' => true,

        'routes' => [
            '/wp-json/wc/v3/products' => [
                'disable_plugins' => [
                    // Elementor ecosystem
                    'elementor/elementor.php',
                    'elementor-pro/elementor-pro.php',
                    'addon-elements-for-elementor-page-builder/addon-elements-for-elementor-page-builder.php',
                    'dynamic-content-for-elementor-new/dynamic-content-for-elementor.php',
                    'dynamic-content-for-elementor-new/dynamic-content-for-elementor-new.php',
                    'visibility-logic-elementor-pro/visibility-logic-elementor-pro.php',
                    'woodmart-core/woodmart-core.php',
                    'welaunch-framework/welaunch-framework.php',

                    // Frontend/login/UI helpers
                    'digits/digit.php',
                    'digits/digits.php',
                    'digoneclickls/digoneclickls.php',
                    'digpagelock/digpagelock.php',
                    'dtr-phone-login/dtr-phone-login.php',
                    'wc-silent-phone-registration/wc-silent-phone-registration.php',
                    'whatsapp-for-wordpress/whatsapp-for-wordpress.php',
                    'couponwheel-old/couponwheel.php',

                    // SEO/tracking/cache/admin tools
                    'wordpress-seo/wp-seo.php',
                    'wordpress-seo-premium/wp-seo-premium.php',
                    'duracelltomi-google-tag-manager/duracelltomi-google-tag-manager.php',
                    'wp-rocket/wp-rocket.php',
                    'powered-cache/powered-cache.php',
                    'query-monitor/query-monitor.php',
                    'wp-crontrol/wp-crontrol.php',
                    'loco-translate/loco.php',
                    'duplicate-menu/duplicate-menu.php',
                    'classic-editor/classic-editor.php',
                    'classic-widgets/classic-widgets.php',
                    'updraftplus/updraftplus.php',
                    'wp-mail-smtp/wp_mail_smtp.php',
                ],
                'disable_hooks' => [
                    'wp_head',
                    'wp_footer',
                ],
                'woocommerce_slim' => true,
            ],

            '/wp-json/wc/v3/products/categories' => [
                'disable_plugins' => [
                    'elementor/elementor.php',
                    'elementor-pro/elementor-pro.php',
                    'woodmart-core/woodmart-core.php',
                    'wordpress-seo/wp-seo.php',
                    'wordpress-seo-premium/wp-seo-premium.php',
                    'wp-rocket/wp-rocket.php',
                    'powered-cache/powered-cache.php',
                    'query-monitor/query-monitor.php',
                ],
                'woocommerce_slim' => true,
            ],
        ],
    ];
}
