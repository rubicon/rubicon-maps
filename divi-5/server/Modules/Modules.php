<?php
// SPDX-License-Identifier: GPL-2.0-or-later

namespace RubiconMaps\Divi5\Modules;

use RubiconMaps\Divi5\Modules\RubiconLocationListModule\RubiconLocationListModule;
use RubiconMaps\Divi5\Modules\RubiconMapModule\RubiconMapModule;

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'divi_module_library_modules_dependency_tree',
    static function ($dependencyTree): void {
        if (!\RubiconMaps\Support\Plugin::isDivi5Enabled()) {
            return;
        }

        $dependencyTree->add_dependency(new RubiconMapModule());
        $dependencyTree->add_dependency(new RubiconLocationListModule());
    }
);
