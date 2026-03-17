<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use RubiconMaps\Frontend\LocationListRenderer;

function assertSameListValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(
            STDERR,
            $message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true) . PHP_EOL
        );
        exit(1);
    }
}

$state = LocationListRenderer::resolveInstanceState(
    [
        'id' => 'rtv_map_abc123',
    ],
    'generated-list-id'
);

assertSameListValue('rtv_map_abc123', $state['instanceId'], 'Explicit Sync IDs should remain the canonical list instance ID.');
assertSameListValue('follow-map', $state['syncMode'], 'Lists with an explicit Sync ID should follow the synced map.');
assertSameListValue(true, $state['isSynced'], 'Lists with an explicit Sync ID should be marked synced.');

$state = LocationListRenderer::resolveInstanceState(
    [
        'id' => '',
    ],
    'generated-list-id'
);

assertSameListValue('generated-list-id', $state['instanceId'], 'Standalone lists should use the generated fallback instance ID.');
assertSameListValue('standalone', $state['syncMode'], 'Lists without a Sync ID should stay independent.');
assertSameListValue(false, $state['isSynced'], 'Lists without a Sync ID should not be marked synced.');

echo 'LocationListRendererTest passed.' . PHP_EOL;
