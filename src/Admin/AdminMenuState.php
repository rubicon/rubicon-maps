<?php

namespace RubiconMaps\Admin;

use RubiconMaps\Support\Plugin;

use function add_filter;
use function get_current_screen;
use function sanitize_key;

if (!defined('ABSPATH')) {
    exit;
}

final class AdminMenuState
{
    public static function init(): void
    {
        add_filter('parent_file', [self::class, 'filterParentFile']);
        add_filter('submenu_file', [self::class, 'filterSubmenuFile']);
    }

    public static function filterParentFile(string $parentFile): string
    {
        return self::resolveParentFile(self::context()) ?? $parentFile;
    }

    public static function filterSubmenuFile(?string $submenuFile): ?string
    {
        return self::resolveSubmenuFile(self::context()) ?? $submenuFile;
    }

    /**
     * @param array<string, string> $context
     */
    public static function resolveParentFile(array $context): ?string
    {
        if (
            Plugin::MENU_PAGE_SLUG === ($context['page'] ?? '')
            || Plugin::SETTINGS_PAGE_SLUG === ($context['page'] ?? '')
            || ImportExportPage::PAGE_SLUG === ($context['page'] ?? '')
            || Plugin::POST_TYPE_LOCATION === ($context['post_type'] ?? '')
            || in_array(($context['taxonomy'] ?? ''), [Plugin::TAXONOMY_CATEGORY, Plugin::TAXONOMY_REGION], true)
        ) {
            return Plugin::MENU_PAGE_SLUG;
        }

        return null;
    }

    /**
     * @param array<string, string> $context
     */
    public static function resolveSubmenuFile(array $context): ?string
    {
        if (Plugin::TAXONOMY_CATEGORY === ($context['taxonomy'] ?? '')) {
            return self::taxonomySubmenu(Plugin::TAXONOMY_CATEGORY);
        }

        if (Plugin::TAXONOMY_REGION === ($context['taxonomy'] ?? '')) {
            return self::taxonomySubmenu(Plugin::TAXONOMY_REGION);
        }

        if (Plugin::POST_TYPE_LOCATION === ($context['post_type'] ?? '')) {
            if ('post-new.php' === ($context['script'] ?? '')) {
                return 'post-new.php?post_type=' . Plugin::POST_TYPE_LOCATION;
            }

            return Plugin::locationMenuSlug();
        }

        if (ImportExportPage::PAGE_SLUG === ($context['page'] ?? '')) {
            return ImportExportPage::PAGE_SLUG;
        }

        if (Plugin::MENU_PAGE_SLUG === ($context['page'] ?? '')) {
            return Plugin::MENU_PAGE_SLUG;
        }

        if (Plugin::SETTINGS_PAGE_SLUG === ($context['page'] ?? '')) {
            return Plugin::SETTINGS_PAGE_SLUG;
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    private static function context(): array
    {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;

        return [
            'page' => sanitize_key((string) ($_GET['page'] ?? '')),
            'post_type' => sanitize_key((string) ($screen->post_type ?? $_GET['post_type'] ?? '')),
            'taxonomy' => sanitize_key((string) ($screen->taxonomy ?? $_GET['taxonomy'] ?? '')),
            'script' => sanitize_key((string) ($GLOBALS['pagenow'] ?? '')),
        ];
    }

    private static function taxonomySubmenu(string $taxonomy): string
    {
        return 'edit-tags.php?taxonomy=' . $taxonomy . '&post_type=' . Plugin::POST_TYPE_LOCATION;
    }
}
