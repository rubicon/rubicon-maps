<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

require_once plugin_dir_path(__FILE__) . 'src/Admin/UninstallHelper.php';
RubiconMaps\Admin\UninstallHelper::uninstall();
