<?php

use RubiconMaps\Support\DiviBootstrapDecider;
use RubiconMaps\Support\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'et_builder_ready',
    static function (): void {
        if (
            !DiviBootstrapDecider::shouldBootDivi4(
                Plugin::isDivi5Enabled(),
                class_exists('\ET_Builder_Module')
            )
        ) {
            return;
        }

        require_once __DIR__ . '/modules/RubiconMapModule.php';
        require_once __DIR__ . '/modules/RubiconLocationListModule.php';

        new \RubiconMaps\Divi4\Modules\RubiconMapModule();
        new \RubiconMaps\Divi4\Modules\RubiconLocationListModule();
    }
);

$enqueueDivi4FilterAsset = static function (): void {
    if (
        !DiviBootstrapDecider::shouldBootDivi4(
            Plugin::isDivi5Enabled(),
            class_exists('\ET_Builder_Module')
        )
    ) {
        return;
    }

    wp_enqueue_script(
        'rtv-rm-divi4-filter-fields',
        plugins_url('/assets/js/divi4-builder-filters.js', RTV_RM_PLUGIN_FILE),
        [],
        Plugin::version(),
        true
    );

    wp_localize_script(
        'rtv-rm-divi4-filter-fields',
        'rubiconMapsDivi4Filters',
        [
            'restBase' => rest_url(),
        ]
    );
};

add_action('admin_enqueue_scripts', $enqueueDivi4FilterAsset);

add_action(
    'wp_enqueue_scripts',
    static function () use ($enqueueDivi4FilterAsset): void {
        if (!isset($_GET['et_fb'])) {
            return;
        }

        $enqueueDivi4FilterAsset();
    }
);
