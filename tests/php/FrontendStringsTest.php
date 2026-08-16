<?php
// SPDX-License-Identifier: GPL-2.0-or-later
declare(strict_types=1);
require_once __DIR__ . '/../../vendor/autoload.php';
if (!defined('ABSPATH')) { define('ABSPATH', __DIR__); }
if (!function_exists('__')) { function __($t, $d = 'default') { return $t; } }

use RubiconMaps\Frontend\FrontendAssetManager;

$strings = FrontendAssetManager::frontendStrings();
$required = ['emptyStandalone', 'emptySynced', 'loading', 'error', 'retry', 'focusOnLocation'];
foreach ($required as $key) {
    if (!array_key_exists($key, $strings) || '' === (string) $strings[$key]) {
        fwrite(STDERR, "Missing localized string: {$key}" . PHP_EOL);
        exit(1);
    }
}
if (!str_contains($strings['focusOnLocation'], '%s')) {
    fwrite(STDERR, 'focusOnLocation must be a sprintf template with %s' . PHP_EOL);
    exit(1);
}
echo 'FrontendStringsTest passed.' . PHP_EOL;
