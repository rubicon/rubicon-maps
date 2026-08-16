<?php
// SPDX-License-Identifier: GPL-2.0-or-later

declare(strict_types=1);

define('ABSPATH', __DIR__ . '/../../');
require_once __DIR__ . '/../../vendor/autoload.php';

use RubiconMaps\Support\PluginUpdater;

function assertSameUpdaterValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(
            STDERR,
            $message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true) . PHP_EOL
        );
        exit(1);
    }
}

$reflection = new ReflectionClass(PluginUpdater::class);

$normalizeVersion = $reflection->getMethod('normalizeVersion');

assertSameUpdaterValue(
    '1.0.0',
    $normalizeVersion->invoke(null, 'v1.0.0'),
    'Updater should normalize release tags that use a leading v prefix.'
);

assertSameUpdaterValue(
    '1.0.0-rc.1',
    $normalizeVersion->invoke(null, '1.0.0-rc.1'),
    'Updater should preserve prerelease version strings when no v prefix exists.'
);

$packageUrl = $reflection->getMethod('packageUrl');

assertSameUpdaterValue(
    'https://github.com/rubicon/rubicon-maps/releases/download/v1.0.0/rubicon-maps-1.0.0.zip',
    $packageUrl->invoke(
        null,
        [
            'assets' => [
                [
                    'name' => 'rubicon-maps-1.0.0.sha256',
                    'browser_download_url' => 'https://github.com/rubicon/rubicon-maps/releases/download/v1.0.0/rubicon-maps-1.0.0.sha256',
                ],
                [
                    'name' => 'rubicon-maps-1.0.0.zip',
                    'browser_download_url' => 'https://github.com/rubicon/rubicon-maps/releases/download/v1.0.0/rubicon-maps-1.0.0.zip',
                ],
            ],
        ]
    ),
    'Updater should prefer the canonical Rubicon Maps zip asset when multiple release assets exist.'
);

// Realistic GitHub Releases API payload: full response shape (tag_name,
// html_url, and assets[] alongside fields the updater doesn't read) should
// resolve to the same asset the minimal fixture above does.
assertSameUpdaterValue(
    'https://github.com/rubicon/rubicon-maps/releases/download/v1.0.1/rubicon-maps-1.0.1.zip',
    $packageUrl->invoke(
        null,
        [
            'tag_name' => 'v1.0.1',
            'html_url' => 'https://github.com/rubicon/rubicon-maps/releases/tag/v1.0.1',
            'assets' => [
                [
                    'name' => 'rubicon-maps-1.0.1.zip',
                    'browser_download_url' => 'https://github.com/rubicon/rubicon-maps/releases/download/v1.0.1/rubicon-maps-1.0.1.zip',
                ],
            ],
        ]
    ),
    'Updater should resolve the package URL from a full GitHub Releases API payload shape.'
);

echo 'PluginUpdaterTest passed.' . PHP_EOL;
