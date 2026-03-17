<?php
namespace RubiconMaps\Admin;

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
            'tile_url' => self::sanitize_tile_url((string) ($input['tile_url'] ?? '')),
            'google_maps_api_key' => sanitize_text_field($input['google_maps_api_key'] ?? ''),
            'enable_scroll_wheel' => !empty($input['enable_scroll_wheel']),
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
            ?>
            <select id="<?php echo esc_attr($args['name']); ?>" name="<?php echo esc_attr(Plugin::OPTION_NAME); ?>[<?php echo esc_attr($args['name']); ?>]">
                <option value="leaflet" <?php selected((string) $value, 'leaflet'); ?>><?php esc_html_e('Leaflet / OpenStreetMap', 'rubicon-maps'); ?></option>
                <option value="google" <?php selected((string) $value, 'google'); ?>><?php esc_html_e('Google Maps', 'rubicon-maps'); ?></option>
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
            'default_latitude' => '29.7604',
            'default_longitude' => '-95.3698',
            'default_zoom' => 9,
            'default_map_height' => '480px',
            'tile_url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            'enable_scroll_wheel' => true,
            default => '',
        };
    }

    private static function description_for(string $settingKey): string
    {
        return match ($settingKey) {
            'default_provider' => __('Leaflet is the recommended provider for v1. Google support can still be configured when you have an API key.', 'rubicon-maps'),
            'default_latitude' => __('Used when a map module or shortcode does not define its own center latitude.', 'rubicon-maps'),
            'default_longitude' => __('Used when a map module or shortcode does not define its own center longitude.', 'rubicon-maps'),
            'default_zoom' => __('Used when a map module or shortcode does not define its own zoom level.', 'rubicon-maps'),
            'default_map_height' => __('Accepts any valid CSS height value, such as 480px, 60vh, or 100%.', 'rubicon-maps'),
            'tile_url' => __('Override the Leaflet tile source when you need a custom tile server.', 'rubicon-maps'),
            'google_maps_api_key' => __('Required only when you intentionally switch a map instance to Google Maps.', 'rubicon-maps'),
            'enable_scroll_wheel' => __('Controls the default mouse-wheel zoom behavior for new map instances.', 'rubicon-maps'),
            default => '',
        };
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
