<?php
// SPDX-License-Identifier: GPL-2.0-or-later

namespace RubiconMaps\Admin;

use RubiconMaps\Support\Plugin;

use function __;
use function add_filter;
use function admin_url;
use function esc_url;
use function sprintf;

if (!defined('ABSPATH')) {
    exit;
}

final class PluginActionLinks
{
    public static function init(): void
    {
        add_filter(
            'plugin_action_links_' . plugin_basename(RTV_RM_PLUGIN_FILE),
            [self::class, 'addSettingsLink']
        );
    }

    /**
     * @param array<int, string> $links
     * @return array<int, string>
     */
    public static function addSettingsLink(array $links): array
    {
        array_unshift(
            $links,
            sprintf(
                '<a href="%1$s">%2$s</a>',
                esc_url(admin_url('admin.php?page=' . Plugin::SETTINGS_PAGE_SLUG)),
                __('Settings', 'rubicon-maps')
            )
        );

        return $links;
    }
}
