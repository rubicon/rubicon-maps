<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use RubiconMaps\ImportExport\LocationCsvTransformer;

function assertSameCsvValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(
            STDERR,
            $message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true) . PHP_EOL
        );
        exit(1);
    }
}

$headers = LocationCsvTransformer::headers();

assertSameCsvValue(
    [
        'id',
        'title',
        'excerpt',
        'content',
        'street',
        'city',
        'state',
        'zip',
        'country',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'categories',
        'regions',
        'featured_image',
        'marker_icon_id',
        'menu_order',
        'status',
    ],
    $headers,
    'CSV headers should expose the canonical Rubicon Maps v1 columns in export order.'
);

assertSameCsvValue(
    array_fill_keys($headers, ''),
    LocationCsvTransformer::templateRow(),
    'The CSV template row should include every canonical column with blank values.'
);

$normalized = LocationCsvTransformer::normalizeImportRow(
    [
        ' ID ' => '42',
        'Title' => ' Downtown Office ',
        'Excerpt' => '  Short summary ',
        'Content' => 'Longer description',
        'Street' => '123 Main St',
        'City' => 'Houston',
        'State' => 'TX',
        'Zip' => '77002',
        'Country' => 'USA',
        'Latitude' => '29.7604',
        'Longitude' => '-95.3698',
        'Phone' => '(713) 555-0100',
        'Email' => ' team@example.com ',
        'Website' => 'https://example.com/location',
        'Categories' => ' retail, flagship ,retail ',
        'Regions' => ' texas, houston ',
        'Featured Image' => 'https://example.com/image.jpg',
        'Marker Icon ID' => '31',
        'Menu Order' => '4',
        'Status' => 'draft',
    ]
);

assertSameCsvValue(42, $normalized['id'], 'Import rows should normalize numeric IDs.');
assertSameCsvValue('Downtown Office', $normalized['title'], 'Import rows should trim the location title.');
assertSameCsvValue('Short summary', $normalized['excerpt'], 'Import rows should trim the excerpt.');
assertSameCsvValue('Longer description', $normalized['content'], 'Import rows should preserve content.');
assertSameCsvValue('123 Main St', $normalized['street'], 'Import rows should capture structured street addresses.');
assertSameCsvValue('Houston', $normalized['city'], 'Import rows should capture the city.');
assertSameCsvValue('TX', $normalized['state'], 'Import rows should capture the state.');
assertSameCsvValue('77002', $normalized['zip'], 'Import rows should capture the zip/postal code.');
assertSameCsvValue('USA', $normalized['country'], 'Import rows should capture the country.');
assertSameCsvValue('29.7604', $normalized['latitude'], 'Import rows should preserve latitude values as canonical strings.');
assertSameCsvValue('-95.3698', $normalized['longitude'], 'Import rows should preserve longitude values as canonical strings.');
assertSameCsvValue('(713) 555-0100', $normalized['phone'], 'Import rows should trim phone values.');
assertSameCsvValue('team@example.com', $normalized['email'], 'Import rows should trim email values.');
assertSameCsvValue('https://example.com/location', $normalized['website'], 'Import rows should trim website values.');
assertSameCsvValue(['retail', 'flagship'], $normalized['categories'], 'Import rows should normalize categories into unique slug-like lists.');
assertSameCsvValue(['texas', 'houston'], $normalized['regions'], 'Import rows should normalize regions into unique slug-like lists.');
assertSameCsvValue('https://example.com/image.jpg', $normalized['featured_image'], 'Import rows should keep featured-image references.');
assertSameCsvValue(31, $normalized['marker_icon_id'], 'Import rows should normalize marker icon IDs.');
assertSameCsvValue(4, $normalized['menu_order'], 'Import rows should normalize menu order.');
assertSameCsvValue('draft', $normalized['status'], 'Import rows should preserve supported post statuses.');

$exportRow = LocationCsvTransformer::buildExportRow(
    [
        'id' => 42,
        'title' => 'Downtown Office',
        'excerpt' => 'Short summary',
        'description' => 'Longer description',
        'address_parts' => [
            'street' => '123 Main St',
            'city' => 'Houston',
            'state' => 'TX',
            'zip' => '77002',
            'country' => 'USA',
        ],
        'latitude' => '29.7604',
        'longitude' => '-95.3698',
        'phone' => '(713) 555-0100',
        'email' => 'team@example.com',
        'website' => 'https://example.com/location',
        'categories' => [
            ['slug' => 'retail'],
            ['slug' => 'flagship'],
        ],
        'regions' => [
            ['slug' => 'texas'],
            ['slug' => 'houston'],
        ],
        'image_url' => 'https://example.com/image.jpg',
        'marker_icon_id' => 31,
        'menu_order' => 4,
        'status' => 'publish',
    ]
);

assertSameCsvValue(
    [
        'id' => '42',
        'title' => 'Downtown Office',
        'excerpt' => 'Short summary',
        'content' => 'Longer description',
        'street' => '123 Main St',
        'city' => 'Houston',
        'state' => 'TX',
        'zip' => '77002',
        'country' => 'USA',
        'latitude' => '29.7604',
        'longitude' => '-95.3698',
        'phone' => '(713) 555-0100',
        'email' => 'team@example.com',
        'website' => 'https://example.com/location',
        'categories' => 'retail,flagship',
        'regions' => 'texas,houston',
        'featured_image' => 'https://example.com/image.jpg',
        'marker_icon_id' => '31',
        'menu_order' => '4',
        'status' => 'publish',
    ],
    $exportRow,
    'Export rows should flatten canonical location payloads back into Rubicon CSV columns.'
);

echo 'LocationCsvTransformerTest passed.' . PHP_EOL;
