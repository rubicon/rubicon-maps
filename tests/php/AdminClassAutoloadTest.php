<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

$classes = [
    \RubiconMaps\Admin\AdminAssets::class,
    \RubiconMaps\Admin\AdminMenuState::class,
    \RubiconMaps\Admin\CategoryMeta::class,
    \RubiconMaps\Admin\GeocodeController::class,
    \RubiconMaps\Admin\ImportExportPage::class,
    \RubiconMaps\Admin\MetaBox::class,
    \RubiconMaps\Admin\PluginActionLinks::class,
    \RubiconMaps\Admin\SettingsPage::class,
];

foreach ($classes as $className) {
    if (!class_exists($className)) {
        fwrite(STDERR, 'Missing admin class: ' . $className . PHP_EOL);
        exit(1);
    }
}

echo 'AdminClassAutoloadTest passed.' . PHP_EOL;
