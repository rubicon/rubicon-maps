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
