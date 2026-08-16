<?php
// SPDX-License-Identifier: GPL-2.0-or-later

declare(strict_types=1);

define('ABSPATH', __DIR__ . '/../../');
require_once __DIR__ . '/../../vendor/autoload.php';

use RubiconMaps\Admin\LocationListFilters;

function assertSameLocationListValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(
            STDERR,
            $message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true) . PHP_EOL
        );
        exit(1);
    }
}

$actions = [
    'edit' => 'Edit',
    'inline hide-if-no-js' => 'Quick Edit',
    'trash' => 'Trash',
    'view' => 'View',
];

$expected = [
    'rtv_rm_location_id' => 'ID: 43',
    'edit' => 'Edit',
    'inline hide-if-no-js' => 'Quick Edit',
    'trash' => 'Trash',
    'view' => 'View',
];

assertSameLocationListValue(
    $expected,
    LocationListFilters::prependInlineIdAction($actions, 43),
    'Location row actions should prepend the post ID before the normal edit actions.'
);

echo 'LocationListFiltersTest passed.' . PHP_EOL;
