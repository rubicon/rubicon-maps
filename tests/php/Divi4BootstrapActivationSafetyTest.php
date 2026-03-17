<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

define('ABSPATH', __DIR__);

$registeredActions = [];

function add_action(string $hook, callable $callback): void
{
    global $registeredActions;
    $registeredActions[$hook] = $callback;
}

require __DIR__ . '/../../divi-4/divi-4.php';

if (!isset($registeredActions['et_builder_ready'])) {
    fwrite(STDERR, 'Divi 4 bootstrap should register an et_builder_ready callback without loading the legacy builder classes during activation.' . PHP_EOL);
    exit(1);
}

echo 'Divi4BootstrapActivationSafetyTest passed.' . PHP_EOL;
