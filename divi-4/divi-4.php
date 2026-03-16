<?php

use RubiconMaps\Support\DiviBootstrapDecider;
use RubiconMaps\Support\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/modules/RubiconMapModule.php';
require_once __DIR__ . '/modules/RubiconLocationListModule.php';

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

        new \RubiconMaps\Divi4\Modules\RubiconMapModule();
        new \RubiconMaps\Divi4\Modules\RubiconLocationListModule();
    }
);
