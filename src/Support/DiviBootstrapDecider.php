<?php

namespace RubiconMaps\Support;

if (!defined('ABSPATH')) {
    exit;
}

final class DiviBootstrapDecider
{
    public static function shouldBootDivi4(bool $isDivi5Enabled, bool $hasLegacyBuilder): bool
    {
        return !$isDivi5Enabled && $hasLegacyBuilder;
    }

    public static function shouldBootDivi5(bool $isDivi5Enabled): bool
    {
        return $isDivi5Enabled;
    }
}
