<?php

use RubiconMaps\Support\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/server/Modules/Modules.php';

add_action(
    'divi_visual_builder_assets_before_enqueue_scripts',
    static function (): void {
        if (
            !Plugin::isDivi5Enabled()
            || !class_exists('\ET\Builder\VisualBuilder\Assets\PackageBuildManager')
        ) {
            return;
        }

        $scriptPath = Plugin::path(Plugin::DIVI5_SCRIPT_RELATIVE_PATH);

        if (!file_exists($scriptPath)) {
            return;
        }

        \ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
            [
                'name' => Plugin::DIVI5_SCRIPT_HANDLE,
                'version' => Plugin::version(),
                'script' => [
                    'src' => Plugin::url(Plugin::DIVI5_SCRIPT_RELATIVE_PATH),
                    'deps' => [
                        'divi-module-library',
                        'divi-vendor-wp-hooks',
                        'react',
                        'jquery-core',
                        'divi-rest',
                        'wp-hooks',
                    ],
                    'enqueue_top_window' => false,
                    'enqueue_app_window' => true,
                ],
            ]
        );
    }
);
