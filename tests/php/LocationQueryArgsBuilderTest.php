<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use RubiconMaps\Frontend\LocationQueryArgsBuilder;
use RubiconMaps\Support\Plugin;

function assertSameValue(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(
            STDERR,
            $message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true) . PHP_EOL
        );
        exit(1);
    }
}

$builder = new LocationQueryArgsBuilder();

$args = $builder->build([
    'category' => ' retail,wholesale ,, retail ',
    'region' => ['texas', 'houston', 'texas'],
    'location_ids' => '5, 7,11, 7',
]);

assertSameValue(Plugin::POST_TYPE_LOCATION, $args['post_type'], 'Location query should target the canonical location post type.');
assertSameValue(['publish'], $args['post_status'], 'Location query should default to published locations only.');
assertSameValue([5, 7, 11], $args['post__in'], 'Location IDs should be normalized into a unique integer list.');
assertSameValue('menu_order title', $args['orderby'], 'Location query should use the canonical ordering.');
assertSameValue('ASC', $args['order'], 'Location query should sort ascending by default.');
assertSameValue('AND', $args['tax_query']['relation'], 'Multiple taxonomy filters should be combined with AND.');
assertSameValue(
    ['retail', 'wholesale'],
    $args['tax_query'][0]['terms'],
    'Category filters should normalize comma-separated slugs.'
);
assertSameValue(
    Plugin::TAXONOMY_CATEGORY,
    $args['tax_query'][0]['taxonomy'],
    'Category filter should use the canonical category taxonomy.'
);
assertSameValue(
    ['texas', 'houston'],
    $args['tax_query'][1]['terms'],
    'Region filters should normalize array slugs.'
);
assertSameValue(
    Plugin::TAXONOMY_REGION,
    $args['tax_query'][1]['taxonomy'],
    'Region filter should use the canonical region taxonomy.'
);

echo 'LocationQueryArgsBuilderTest passed.' . PHP_EOL;
