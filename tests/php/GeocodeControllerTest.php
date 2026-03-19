<?php

declare(strict_types=1);

define('ABSPATH', __DIR__ . '/../../');

function add_action(string $hook, callable $callback): void
{
}

function sanitize_text_field(string $value): string
{
    return trim(strip_tags($value));
}

function __(string $text, string $domain = ''): string
{
    return $text;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use RubiconMaps\Admin\GeocodeController;

function assertGeocodeSame(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(
            STDERR,
            $message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true) . PHP_EOL
        );
        exit(1);
    }
}

$results = GeocodeController::buildResultsPayload([
    [
        'lat' => '32.482392',
        'lon' => '-96.944572',
        'display_name' => '4017 Pecan Rd, Midlothian, Texas',
        'address' => [
            'house_number' => '4017',
            'road' => 'Pecan Rd',
            'city' => 'Midlothian',
            'state' => 'Texas',
            'postcode' => '76065',
            'country' => 'United States',
        ],
    ],
]);

assertGeocodeSame('32.482392', $results[0]['latitude'], 'Autocomplete payloads should preserve latitude.');
assertGeocodeSame('-96.944572', $results[0]['longitude'], 'Autocomplete payloads should preserve longitude.');
assertGeocodeSame('4017 Pecan Rd', $results[0]['street'], 'Autocomplete payloads should combine house number and road.');
assertGeocodeSame('Midlothian', $results[0]['city'], 'Autocomplete payloads should populate city.');
assertGeocodeSame('Texas', $results[0]['state'], 'Autocomplete payloads should populate state.');
assertGeocodeSame('76065', $results[0]['zip'], 'Autocomplete payloads should populate zip.');
assertGeocodeSame('United States', $results[0]['country'], 'Autocomplete payloads should populate country.');

echo 'GeocodeControllerTest passed.' . PHP_EOL;
