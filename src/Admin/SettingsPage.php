<?php
namespace RubiconMaps\Admin;

use RubiconMaps\Helpers\SettingsHelper;
use RubiconMaps\Support\Plugin;

class SettingsPage {

	public static function init() {
		add_action('admin_menu', [self::class, 'add_settings_page']);
		add_action('admin_init', [self::class, 'register_settings']);
	}

	public static function add_settings_page() {
		add_menu_page(
			__('Rubicon Maps', 'rubicon-maps'),
			__('Rubicon Maps', 'rubicon-maps'),
			'manage_options',
			Plugin::SETTINGS_PAGE_SLUG,
			[self::class, 'render_settings_page'],
			'dashicons-location-alt',
			60
		);

        add_submenu_page(
            Plugin::SETTINGS_PAGE_SLUG,
            __('Settings', 'rubicon-maps'),
            __('Settings', 'rubicon-maps'),
            'manage_options',
            Plugin::SETTINGS_PAGE_SLUG,
            [self::class, 'render_settings_page']
        );
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
			'default_latitude' => ['label' => __('Default Latitude', 'rubicon-maps'), 'type' => 'text'],
			'default_longitude' => ['label' => __('Default Longitude', 'rubicon-maps'), 'type' => 'text'],
			'default_zoom' => ['label' => __('Default Zoom Level', 'rubicon-maps'), 'type' => 'number'],
			'default_map_height' => ['label' => __('Default Map Height', 'rubicon-maps'), 'type' => 'text'],
			'tile_url' => ['label' => __('Leaflet Tile URL', 'rubicon-maps'), 'type' => 'text'],
			'google_maps_api_key' => ['label' => __('Google Maps API Key', 'rubicon-maps'), 'type' => 'text'],
			'enable_scroll_wheel' => ['label' => __('Enable Scroll Wheel Zoom', 'rubicon-maps'), 'type' => 'checkbox'],
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
            'default_provider' => in_array($input['default_provider'] ?? 'leaflet', ['leaflet', 'google'], true) ? $input['default_provider'] : 'leaflet',
            'default_latitude' => sanitize_text_field($input['default_latitude'] ?? ''),
            'default_longitude' => sanitize_text_field($input['default_longitude'] ?? ''),
            'default_zoom' => max(1, (int) ($input['default_zoom'] ?? 9)),
            'default_map_height' => sanitize_text_field($input['default_map_height'] ?? '480px'),
            'tile_url' => esc_url_raw($input['tile_url'] ?? ''),
            'google_maps_api_key' => sanitize_text_field($input['google_maps_api_key'] ?? ''),
            'enable_scroll_wheel' => !empty($input['enable_scroll_wheel']),
        ];
    }

	public static function render_field($args) {
		$value = SettingsHelper::get_option($args['name'], self::default_for($args['name']));

        if ('checkbox' === $args['type']) {
            printf(
                '<label><input type="checkbox" id="%1$s" name="%2$s[%1$s]" value="1" %3$s /> %4$s</label>',
                esc_attr($args['name']),
                esc_attr(Plugin::OPTION_NAME),
                checked((bool) $value, true, false),
                esc_html__('Enabled', 'rubicon-maps')
            );

            return;
        }

        if ('select' === $args['type']) {
            ?>
            <select id="<?php echo esc_attr($args['name']); ?>" name="<?php echo esc_attr(Plugin::OPTION_NAME); ?>[<?php echo esc_attr($args['name']); ?>]">
                <option value="leaflet" <?php selected((string) $value, 'leaflet'); ?>><?php esc_html_e('Leaflet / OpenStreetMap', 'rubicon-maps'); ?></option>
                <option value="google" <?php selected((string) $value, 'google'); ?>><?php esc_html_e('Google Maps', 'rubicon-maps'); ?></option>
            </select>
            <?php
            return;
        }

		printf(
            '<input type="%1$s" id="%2$s" name="%3$s[%2$s]" value="%4$s" class="regular-text" />',
            esc_attr('number' === $args['type'] ? 'number' : 'text'),
            esc_attr($args['name']),
            esc_attr(Plugin::OPTION_NAME),
            esc_attr((string) $value)
        );
	}

	public static function render_settings_page() {
		?>
		<div class="wrap">
			<h1><?php _e('Rubicon Maps Settings', 'rubicon-maps'); ?></h1>
            <p><?php esc_html_e('Configure the default behavior for Rubicon Maps and the Divi modules that render your map instances.', 'rubicon-maps'); ?></p>
			<form method="post" action="options.php">
				<?php
				settings_fields(Plugin::OPTION_GROUP);
				do_settings_sections(Plugin::SETTINGS_PAGE_SLUG);
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

    private static function default_for(string $settingKey): mixed
    {
        return match ($settingKey) {
            'default_provider' => 'leaflet',
            'default_latitude' => '29.7604',
            'default_longitude' => '-95.3698',
            'default_zoom' => 9,
            'default_map_height' => '480px',
            'tile_url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            'enable_scroll_wheel' => true,
            default => '',
        };
    }
}
