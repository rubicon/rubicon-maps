<?php

namespace RubiconMaps\Admin;

use RubiconMaps\Frontend\FrontendAssetManager;
use RubiconMaps\Support\Plugin;

use function admin_url;
use function get_current_screen;
use function plugins_url;
use function wp_create_nonce;
use function wp_enqueue_media;
use function wp_enqueue_script;
use function wp_enqueue_style;
use function wp_localize_script;

if (!defined('ABSPATH')) {
    exit;
}

final class AdminAssets
{
    public static function init(): void
    {
        add_action('admin_enqueue_scripts', [self::class, 'enqueue']);
    }

    public static function enqueue(): void
    {
        $screen = get_current_screen();

        if (!$screen) {
            return;
        }

        $isRubiconScreen = Plugin::POST_TYPE_LOCATION === $screen->post_type
            || Plugin::TAXONOMY_CATEGORY === $screen->taxonomy
            || Plugin::TAXONOMY_REGION === $screen->taxonomy
            || false !== strpos((string) $screen->id, Plugin::MENU_PAGE_SLUG)
            || false !== strpos((string) $screen->id, Plugin::SETTINGS_PAGE_SLUG);

        if (!$isRubiconScreen) {
            return;
        }

        FrontendAssetManager::registerTokensStyle();

        wp_enqueue_style(
            'rtv-rm-admin',
            plugins_url('/assets/css/rubicon-maps-admin.css', RTV_RM_PLUGIN_FILE),
            ['rtv-rm-tokens'],
            Plugin::version()
        );

        wp_enqueue_media();

        wp_enqueue_script(
            'rtv-rm-admin-meta',
            plugins_url('/assets/js/admin-meta.js', RTV_RM_PLUGIN_FILE),
            ['jquery'],
            Plugin::version(),
            true
        );

        wp_localize_script(
            'rtv-rm-admin-meta',
            'rubiconMapsAdmin',
            [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'geocodeNonce' => wp_create_nonce(GeocodeController::NONCE_ACTION),
                'strings' => [
                    'searching' => __('Looking up this address…', 'rubicon-maps'),
                    'searchError' => __('Unable to find a matching address right now.', 'rubicon-maps'),
                    'searchPrompt' => __('Start typing to search for a matching address.', 'rubicon-maps'),
                    'searchEmpty' => __('No matching addresses were found.', 'rubicon-maps'),
                    'selectImage' => __('Select Icon', 'rubicon-maps'),
                    'useImage' => __('Use this image', 'rubicon-maps'),
                ],
            ]
        );
    }
}
