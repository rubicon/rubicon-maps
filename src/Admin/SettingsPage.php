<?php
// SPDX-License-Identifier: GPL-2.0-or-later
namespace RubiconMaps\Admin;

use RubiconMaps\Frontend\MapInstanceConfigBuilder;
use RubiconMaps\Helpers\SettingsHelper;
use RubiconMaps\Support\Plugin;
use function remove_submenu_page;

class SettingsPage {

	public static function init() {
		add_action('admin_menu', [self::class, 'add_settings_page']);
		add_action('admin_menu', [self::class, 'remove_default_submenu'], 999);
		add_action('admin_init', [self::class, 'register_settings']);
	}

	public static function add_settings_page() {
        add_menu_page(
            __('Rubicon Maps', 'rubicon-maps'),
            __('Rubicon Maps', 'rubicon-maps'),
            'edit_posts',
            Plugin::MENU_PAGE_SLUG,
            '__return_null',
            'dashicons-location-alt',
            60
        );

        add_submenu_page(
            Plugin::MENU_PAGE_SLUG,
            __('Locations', 'rubicon-maps'),
            __('Locations', 'rubicon-maps'),
            'edit_posts',
            Plugin::locationMenuSlug()
        );

        add_submenu_page(
            Plugin::MENU_PAGE_SLUG,
            __('Add New', 'rubicon-maps'),
            __('Add New', 'rubicon-maps'),
            'edit_posts',
            'post-new.php?post_type=' . Plugin::POST_TYPE_LOCATION
        );

        add_submenu_page(
            Plugin::MENU_PAGE_SLUG,
            __('Settings', 'rubicon-maps'),
            __('Settings', 'rubicon-maps'),
            'manage_options',
            Plugin::SETTINGS_PAGE_SLUG,
            [self::class, 'render_settings_page']
        );

        add_submenu_page(
            Plugin::MENU_PAGE_SLUG,
            __('Categories', 'rubicon-maps'),
            __('Categories', 'rubicon-maps'),
            'manage_categories',
            'edit-tags.php?taxonomy=' . Plugin::TAXONOMY_CATEGORY . '&post_type=' . Plugin::POST_TYPE_LOCATION
        );

        add_submenu_page(
            Plugin::MENU_PAGE_SLUG,
            __('Regions', 'rubicon-maps'),
            __('Regions', 'rubicon-maps'),
            'manage_categories',
            'edit-tags.php?taxonomy=' . Plugin::TAXONOMY_REGION . '&post_type=' . Plugin::POST_TYPE_LOCATION
        );
	}

    public static function remove_default_submenu() {
        remove_submenu_page(Plugin::MENU_PAGE_SLUG, Plugin::MENU_PAGE_SLUG);
    }

	public static function register_settings() {
		register_setting(
            Plugin::OPTION_GROUP,
            Plugin::OPTION_NAME,
            [self::class, 'sanitize_settings']
        );

		add_settings_section(
			'rubicon_maps_main',
			__('Map Settings', 'rubicon-maps'),
			null,
			Plugin::SETTINGS_PAGE_SLUG
		);

		$fields = [
			'default_provider' => ['label' => __('Default Provider', 'rubicon-maps'), 'type' => 'select'],
			'default_viewport_mode' => ['label' => __('Default Viewport Mode', 'rubicon-maps'), 'type' => 'select'],
			'default_latitude' => ['label' => __('Default Latitude', 'rubicon-maps'), 'type' => 'text'],
			'default_longitude' => ['label' => __('Default Longitude', 'rubicon-maps'), 'type' => 'text'],
			'default_zoom' => ['label' => __('Default Zoom Level', 'rubicon-maps'), 'type' => 'number'],
			'default_map_height' => ['label' => __('Default Map Height', 'rubicon-maps'), 'type' => 'text'],
			'default_auto_fit_padding' => ['label' => __('Default Auto-fit Padding', 'rubicon-maps'), 'type' => 'number'],
			'default_tile_preset' => ['label' => __('Default Tile Preset', 'rubicon-maps'), 'type' => 'select'],
			'tile_url' => ['label' => __('Leaflet Tile URL', 'rubicon-maps'), 'type' => 'text'],
			'default_enable_zoom_control' => ['label' => __('Show Zoom Control By Default', 'rubicon-maps'), 'type' => 'checkbox'],
			'enable_scroll_wheel' => ['label' => __('Enable Scroll Wheel Zoom', 'rubicon-maps'), 'type' => 'checkbox'],
			'default_enable_double_click_zoom' => ['label' => __('Enable Double-click Zoom By Default', 'rubicon-maps'), 'type' => 'checkbox'],
			'default_popup_trigger' => ['label' => __('Default Popup Trigger', 'rubicon-maps'), 'type' => 'select'],
			'default_popup_max_width' => ['label' => __('Default Popup Max Width', 'rubicon-maps'), 'type' => 'number'],
			'default_close_on_map_click' => ['label' => __('Close Popup On Map Click By Default', 'rubicon-maps'), 'type' => 'checkbox'],
			'default_auto_close_popup' => ['label' => __('Auto-close Popups By Default', 'rubicon-maps'), 'type' => 'checkbox'],
			'default_open_all_popups' => ['label' => __('Open All Popups By Default', 'rubicon-maps'), 'type' => 'checkbox'],
			'default_enable_clustering' => ['label' => __('Enable Marker Clustering By Default', 'rubicon-maps'), 'type' => 'checkbox'],
			'default_cluster_radius' => ['label' => __('Default Cluster Radius', 'rubicon-maps'), 'type' => 'number'],
		];

		foreach ($fields as $key => $field) {
			add_settings_field(
				$key,
				$field['label'],
				[self::class, 'render_field'],
				Plugin::SETTINGS_PAGE_SLUG,
				'rubicon_maps_main',
				[
					'label_for' => $key,
					'name' => $key,
                    'type' => $field['type'],
				]
			);
		}
	}

    /**
     * Sanitize the settings payload before persisting it.
     *
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public static function sanitize_settings(array $input): array
    {
        return [
            'default_provider' => 'leaflet',
            'default_viewport_mode' => in_array($input['default_viewport_mode'] ?? 'auto_fit', ['auto_fit', 'manual'], true) ? $input['default_viewport_mode'] : 'auto_fit',
            'default_latitude' => sanitize_text_field($input['default_latitude'] ?? ''),
            'default_longitude' => sanitize_text_field($input['default_longitude'] ?? ''),
            'default_zoom' => max(1, (int) ($input['default_zoom'] ?? 9)),
            'default_map_height' => sanitize_text_field($input['default_map_height'] ?? '480px'),
            'default_auto_fit_padding' => max(0, (int) ($input['default_auto_fit_padding'] ?? 24)),
            'default_tile_preset' => self::sanitize_tile_preset((string) ($input['default_tile_preset'] ?? 'openstreetmap')),
            'tile_url' => self::sanitize_tile_url((string) ($input['tile_url'] ?? '')),
            'google_maps_api_key' => sanitize_text_field($input['google_maps_api_key'] ?? ''),
            'default_enable_zoom_control' => !empty($input['default_enable_zoom_control']),
            'enable_scroll_wheel' => !empty($input['enable_scroll_wheel']),
            'default_enable_double_click_zoom' => !empty($input['default_enable_double_click_zoom']),
            'default_popup_trigger' => in_array($input['default_popup_trigger'] ?? 'click', ['click', 'hover'], true) ? $input['default_popup_trigger'] : 'click',
            'default_popup_max_width' => max(120, (int) ($input['default_popup_max_width'] ?? 320)),
            'default_close_on_map_click' => !empty($input['default_close_on_map_click']),
            'default_auto_close_popup' => !empty($input['default_auto_close_popup']),
            'default_open_all_popups' => !empty($input['default_open_all_popups']),
            'default_enable_clustering' => !empty($input['default_enable_clustering']),
            'default_cluster_radius' => max(10, (int) ($input['default_cluster_radius'] ?? 100)),
        ];
    }

	public static function render_field($args) {
		$value = SettingsHelper::get_option($args['name'], self::default_for($args['name']));

        if ('checkbox' === $args['type']) {
            printf(
                '<label><input type="checkbox" id="%1$s" name="%2$s[%1$s]" value="1" %3$s /> %4$s</label><p class="description">%5$s</p>',
                esc_attr($args['name']),
                esc_attr(Plugin::OPTION_NAME),
                checked((bool) $value, true, false),
                esc_html__('Enabled', 'rubicon-maps'),
                esc_html(self::description_for($args['name']))
            );

            return;
        }

        if ('select' === $args['type']) {
            $options = self::options_for($args['name']);
            ?>
            <select id="<?php echo esc_attr($args['name']); ?>" name="<?php echo esc_attr(Plugin::OPTION_NAME); ?>[<?php echo esc_attr($args['name']); ?>]">
                <?php foreach ($options as $optionValue => $optionLabel) : ?>
                    <option value="<?php echo esc_attr($optionValue); ?>" <?php selected((string) $value, (string) $optionValue); ?>><?php echo esc_html($optionLabel); ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description"><?php echo esc_html(self::description_for($args['name'])); ?></p>
            <?php
            return;
        }

		printf(
            '<input type="%1$s" id="%2$s" name="%3$s[%2$s]" value="%4$s" class="regular-text" /><p class="description">%5$s</p>',
            esc_attr('number' === $args['type'] ? 'number' : 'text'),
            esc_attr($args['name']),
            esc_attr(Plugin::OPTION_NAME),
            esc_attr((string) $value),
            esc_html(self::description_for($args['name']))
        );
	}

	public static function render_settings_page() {
		?>
		<div class="wrap rubicon-admin">
			<h1><?php _e('Rubicon Maps Settings', 'rubicon-maps'); ?></h1>
            <p><?php esc_html_e('Set the defaults that power your Divi modules, shortcodes, and frontend map instances.', 'rubicon-maps'); ?></p>
            <?php if (!empty($_GET['settings-updated'])) : ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e('Rubicon Maps settings were applied successfully.', 'rubicon-maps'); ?></p>
                </div>
            <?php endif; ?>
			<div class="card" style="max-width:1100px;padding:24px;">
                <div class="rubicon-admin-panel__header" style="margin-bottom:18px;">
                    <div>
                        <p class="rubicon-admin-panel__eyebrow"><?php esc_html_e('Core defaults', 'rubicon-maps'); ?></p>
                        <h2 class="rubicon-admin-panel__title"><?php esc_html_e('Map runtime settings', 'rubicon-maps'); ?></h2>
                    </div>
                    <p class="rubicon-admin-panel__copy"><?php esc_html_e('These defaults apply everywhere unless a shortcode or Divi module overrides them per instance.', 'rubicon-maps'); ?></p>
                </div>
                <form method="post" action="options.php">
                    <?php
                    settings_fields(Plugin::OPTION_GROUP);
                    do_settings_sections(Plugin::SETTINGS_PAGE_SLUG);
                    submit_button(__('Save Rubicon Maps Settings', 'rubicon-maps'));
                    ?>
                </form>
            </div>
		</div>
		<?php
	}

    private static function default_for(string $settingKey): mixed
    {
        return match ($settingKey) {
            'default_provider' => 'leaflet',
            'default_viewport_mode' => 'auto_fit',
            'default_latitude' => '29.7604',
            'default_longitude' => '-95.3698',
            'default_zoom' => 9,
            'default_map_height' => '480px',
            'default_auto_fit_padding' => 24,
            'default_tile_preset' => 'openstreetmap',
            'tile_url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            'default_enable_zoom_control' => true,
            'enable_scroll_wheel' => false,
            'default_enable_double_click_zoom' => true,
            'default_popup_trigger' => 'click',
            'default_popup_max_width' => 320,
            'default_close_on_map_click' => true,
            'default_auto_close_popup' => true,
            'default_open_all_popups' => false,
            'default_enable_clustering' => true,
            'default_cluster_radius' => 100,
            default => '',
        };
    }

    private static function description_for(string $settingKey): string
    {
        return match ($settingKey) {
            'default_provider' => __('Leaflet is the supported provider path for v1.', 'rubicon-maps'),
            'default_viewport_mode' => __('Auto-fit uses the displayed locations to determine the viewport. Manual uses the configured latitude, longitude, and zoom.', 'rubicon-maps'),
            'default_latitude' => __('Used when a map module or shortcode does not define its own center latitude.', 'rubicon-maps'),
            'default_longitude' => __('Used when a map module or shortcode does not define its own center longitude.', 'rubicon-maps'),
            'default_zoom' => __('Used when a map module or shortcode does not define its own zoom level.', 'rubicon-maps'),
            'default_map_height' => __('Accepts any valid CSS height value, such as 480px, 60vh, or 100%.', 'rubicon-maps'),
            'default_auto_fit_padding' => __('Applied when a map is fitting its viewport to the displayed locations.', 'rubicon-maps'),
            'default_tile_preset' => __('Choose the default Leaflet tile preset for new maps. Select custom to use the raw tile URL field below.', 'rubicon-maps'),
            'tile_url' => __('Override the Leaflet tile source when you need a custom tile server.', 'rubicon-maps'),
            'google_maps_api_key' => __('Reserved for future Google provider support.', 'rubicon-maps'),
            'default_enable_zoom_control' => __('Controls whether new map instances show the built-in zoom buttons by default.', 'rubicon-maps'),
            'enable_scroll_wheel' => __('Controls the default mouse-wheel zoom behavior for new map instances. Leave this off if you want pages to scroll naturally until a user intentionally interacts with the map.', 'rubicon-maps'),
            'default_enable_double_click_zoom' => __('Controls the default double-click zoom behavior for new map instances.', 'rubicon-maps'),
            'default_popup_trigger' => __('Controls how map popups are opened by default.', 'rubicon-maps'),
            'default_popup_max_width' => __('Used when a map instance does not define its own popup max width.', 'rubicon-maps'),
            'default_close_on_map_click' => __('When enabled, clicking the map canvas closes the active popup by default.', 'rubicon-maps'),
            'default_auto_close_popup' => __('When enabled, opening a popup automatically closes the previously opened popup by default.', 'rubicon-maps'),
            'default_open_all_popups' => __('When enabled, maps try to open all popups after rendering. Leave this off for the normal single-open experience.', 'rubicon-maps'),
            'default_enable_clustering' => __('Controls whether marker clustering is enabled for new map instances by default.', 'rubicon-maps'),
            'default_cluster_radius' => __('Used as the default maximum cluster radius when a map instance does not override it.', 'rubicon-maps'),
            default => '',
        };
    }

    /**
     * @return array<string, string>
     */
    private static function options_for(string $settingKey): array
    {
        return match ($settingKey) {
            'default_provider' => [
                'leaflet' => __('Leaflet / OpenStreetMap', 'rubicon-maps'),
            ],
            'default_viewport_mode' => [
                'auto_fit' => __('Auto-fit displayed locations', 'rubicon-maps'),
                'manual' => __('Manual center and zoom', 'rubicon-maps'),
            ],
            'default_tile_preset' => self::tilePresetOptions(),
            'default_popup_trigger' => [
                'click' => __('On Click', 'rubicon-maps'),
                'hover' => __('On Hover', 'rubicon-maps'),
            ],
            default => [],
        };
    }

    /**
     * @return array<string, string>
     */
    private static function tilePresetOptions(): array
    {
        return [
            'openstreetmap' => __('OpenStreetMap', 'rubicon-maps'),
            'carto_light' => __('CARTO Light', 'rubicon-maps'),
            'carto_dark' => __('CARTO Dark', 'rubicon-maps'),
            'opentopomap' => __('OpenTopoMap', 'rubicon-maps'),
            'custom' => __('Custom Tile URL', 'rubicon-maps'),
        ];
    }

    private static function sanitize_tile_preset(string $tilePreset): string
    {
        $tilePreset = strtolower(trim($tilePreset));
        $validPresets = array_keys(self::tilePresetOptions());

        return in_array($tilePreset, $validPresets, true) ? $tilePreset : 'openstreetmap';
    }

    private static function sanitize_tile_url(string $tileUrl): string
    {
        $tileUrl = trim($tileUrl);

        if ('' === $tileUrl) {
            return 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        }

        $placeholders = [
            '{s}' => '__RTV_TILE_S__',
            '{z}' => '__RTV_TILE_Z__',
            '{x}' => '__RTV_TILE_X__',
            '{y}' => '__RTV_TILE_Y__',
        ];

        $protectedUrl = strtr($tileUrl, $placeholders);
        $sanitizedUrl = esc_url_raw($protectedUrl);

        if ('' === $sanitizedUrl) {
            return 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        }

        $sanitizedUrl = strtr($sanitizedUrl, array_flip($placeholders));

        foreach (['{z}', '{x}', '{y}'] as $requiredPlaceholder) {
            if (!str_contains($sanitizedUrl, $requiredPlaceholder)) {
                return 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
            }
        }

        return $sanitizedUrl;
    }
}
