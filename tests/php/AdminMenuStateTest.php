<?php
// SPDX-License-Identifier: GPL-2.0-or-later

declare(strict_types=1);

define('ABSPATH', __DIR__ . '/../../');
require_once __DIR__ . '/../../vendor/autoload.php';

use RubiconMaps\Admin\AdminMenuState;
use RubiconMaps\Support\Plugin;

function assertSameMenuValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(
            STDERR,
            $message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true) . PHP_EOL
        );
        exit(1);
    }
}

assertSameMenuValue(
    Plugin::MENU_PAGE_SLUG,
    AdminMenuState::resolveParentFile([
        'page' => Plugin::SETTINGS_PAGE_SLUG,
        'post_type' => '',
        'taxonomy' => '',
    ]),
    'Settings page should keep the Rubicon Maps top-level menu open.'
);

assertSameMenuValue(
    Plugin::MENU_PAGE_SLUG,
    AdminMenuState::resolveParentFile([
        'page' => 'rubicon-maps-import-export',
        'post_type' => '',
        'taxonomy' => '',
    ]),
    'Import / Export should keep the Rubicon Maps top-level menu open.'
);

assertSameMenuValue(
    Plugin::MENU_PAGE_SLUG,
    AdminMenuState::resolveParentFile([
        'page' => '',
        'post_type' => Plugin::POST_TYPE_LOCATION,
        'taxonomy' => Plugin::TAXONOMY_CATEGORY,
    ]),
    'Location taxonomies should keep the Rubicon Maps top-level menu open.'
);

assertSameMenuValue(
    Plugin::locationMenuSlug(),
    AdminMenuState::resolveSubmenuFile([
        'page' => '',
        'post_type' => Plugin::POST_TYPE_LOCATION,
        'taxonomy' => '',
        'script' => 'edit.php',
    ]),
    'Location editing screens should highlight the Locations submenu.'
);

assertSameMenuValue(
    'post-new.php?post_type=' . Plugin::POST_TYPE_LOCATION,
    AdminMenuState::resolveSubmenuFile([
        'page' => '',
        'post_type' => Plugin::POST_TYPE_LOCATION,
        'taxonomy' => '',
        'script' => 'post-new.php',
    ]),
    'New location screens should highlight the Add New submenu.'
);

assertSameMenuValue(
    'edit-tags.php?taxonomy=' . Plugin::TAXONOMY_CATEGORY . '&post_type=' . Plugin::POST_TYPE_LOCATION,
    AdminMenuState::resolveSubmenuFile([
        'page' => '',
        'post_type' => Plugin::POST_TYPE_LOCATION,
        'taxonomy' => Plugin::TAXONOMY_CATEGORY,
        'script' => 'edit-tags.php',
    ]),
    'Category taxonomy screens should highlight the Categories submenu.'
);

assertSameMenuValue(
    'edit-tags.php?taxonomy=' . Plugin::TAXONOMY_REGION . '&post_type=' . Plugin::POST_TYPE_LOCATION,
    AdminMenuState::resolveSubmenuFile([
        'page' => '',
        'post_type' => Plugin::POST_TYPE_LOCATION,
        'taxonomy' => Plugin::TAXONOMY_REGION,
        'script' => 'edit-tags.php',
    ]),
    'Region taxonomy screens should highlight the Regions submenu.'
);

assertSameMenuValue(
    Plugin::SETTINGS_PAGE_SLUG,
    AdminMenuState::resolveSubmenuFile([
        'page' => Plugin::SETTINGS_PAGE_SLUG,
        'post_type' => '',
        'taxonomy' => '',
        'script' => 'admin.php',
    ]),
    'Settings page should highlight the Settings submenu.'
);

assertSameMenuValue(
    Plugin::MENU_PAGE_SLUG,
    AdminMenuState::resolveSubmenuFile([
        'page' => Plugin::MENU_PAGE_SLUG,
        'post_type' => '',
        'taxonomy' => '',
        'script' => 'admin.php',
    ]),
    'The top-level Rubicon Maps menu should highlight the Locations submenu.'
);

echo 'AdminMenuStateTest passed.' . PHP_EOL;
