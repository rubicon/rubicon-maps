<?php

namespace RubiconMaps\Frontend;

final class LocationAddressFormatter
{
    /**
     * Convert structured address parts into a display-friendly address string.
     *
     * @param array<string, string> $parts Address parts keyed by field name.
     */
    public static function format(array $parts): string
    {
        $orderedParts = [
            trim((string) ($parts['street'] ?? '')),
            trim((string) ($parts['city'] ?? '')),
            trim((string) ($parts['state'] ?? '')),
            trim((string) ($parts['zip'] ?? '')),
            trim((string) ($parts['country'] ?? '')),
        ];

        return implode(', ', array_values(array_filter($orderedParts, static fn (string $value): bool => '' !== $value)));
    }
}
