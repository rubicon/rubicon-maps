<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use RubiconMaps\Frontend\LocationAddressFormatter;

function assertSameAddress(string $expected, string $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(
            STDERR,
            $message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true) . PHP_EOL
        );
        exit(1);
    }
}

$formatted = LocationAddressFormatter::format([
    'street' => '123 Main St',
    'city' => 'Houston',
    'state' => 'TX',
    'zip' => '77001',
    'country' => 'USA',
]);

assertSameAddress(
    '123 Main St, Houston, TX, 77001, USA',
    $formatted,
    'Formatted addresses should join the structured address parts in display order.'
);

$formatted = LocationAddressFormatter::format([
    'street' => '123 Main St',
    'city' => '',
    'state' => 'TX',
    'zip' => '',
    'country' => 'USA',
]);

assertSameAddress(
    '123 Main St, TX, USA',
    $formatted,
    'Formatted addresses should drop empty values without leaving duplicate separators.'
);

echo 'LocationAddressFormatterTest passed.' . PHP_EOL;
