<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use RubiconMaps\Support\DiviBootstrapDecider;

function assertSameDecision(mixed $expected, mixed $actual, string $message): void
{
    if ($expected !== $actual) {
        fwrite(
            STDERR,
            $message . PHP_EOL . 'Expected: ' . var_export($expected, true) . PHP_EOL . 'Actual:   ' . var_export($actual, true) . PHP_EOL
        );
        exit(1);
    }
}

assertSameDecision(
    true,
    DiviBootstrapDecider::shouldBootDivi4(false, true),
    'Divi 4 bootstrap should run when the legacy builder exists and Divi 5 is not active.'
);

assertSameDecision(
    false,
    DiviBootstrapDecider::shouldBootDivi4(true, true),
    'Divi 4 bootstrap should not run when Divi 5 is active.'
);

assertSameDecision(
    false,
    DiviBootstrapDecider::shouldBootDivi4(false, false),
    'Divi 4 bootstrap should not run when the legacy builder is unavailable.'
);

assertSameDecision(
    true,
    DiviBootstrapDecider::shouldBootDivi5(true),
    'Divi 5 bootstrap should run when Divi 5 is active.'
);

assertSameDecision(
    false,
    DiviBootstrapDecider::shouldBootDivi5(false),
    'Divi 5 bootstrap should stay idle when Divi 5 is not active.'
);

echo 'DiviBootstrapDeciderTest passed.' . PHP_EOL;
