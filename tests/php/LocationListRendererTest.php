<?php

declare(strict_types=1);

define('ABSPATH', __DIR__ . '/../../');
define('RTV_RM_PLUGIN_FILE', __DIR__ . '/../../rubicon-maps.php');

function esc_attr(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES);
}

function esc_html(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES);
}

function esc_html__(string $value, string $domain = ''): string
{
    return $value;
}

function __(string $value, string $domain = ''): string
{
    return $value;
}

function esc_url(string $value): string
{
    return $value;
}

function wp_kses_post(string $value): string
{
    return $value;
}

function wp_unique_id(): string
{
    return 'test-uid';
}

final class WP_Post
{
    public int $ID = 0;
    public string $post_excerpt = '';
    public string $post_content = '';
}

/**
 * Seeded per-post fixture data, keyed by post ID, consumed by the WP function
 * stubs below (get_post, get_post_meta, get_the_title, etc.) so that a
 * WP_Query result can be turned into a fully-formed location payload by
 * LocationRepository::serialize().
 *
 * @var array<int, array<string, mixed>>
 */
$GLOBALS['rtv_rm_test_posts'] = [];

/**
 * @param array<string, mixed> $data
 */
function rtv_rm_test_seed_post(int $postId, array $data): void
{
    $GLOBALS['rtv_rm_test_posts'][$postId] = $data;
}

function rtv_rm_test_reset_posts(): void
{
    $GLOBALS['rtv_rm_test_posts'] = [];
}

/**
 * @return array<string, mixed>
 */
function rtv_rm_test_post_data(int $postId): array
{
    return $GLOBALS['rtv_rm_test_posts'][$postId] ?? [];
}

final class WP_Query
{
    /**
     * @var array<int, int>
     */
    private static array $nextResultIds = [];

    /**
     * @var array<int, int>
     */
    private array $resultIds;

    private int $cursor = 0;

    public WP_Post $post;

    public function __construct(array $args = [])
    {
        $this->resultIds = self::$nextResultIds;
        self::$nextResultIds = [];
    }

    /**
     * Queue the post IDs the next WP_Query instance should return.
     *
     * @param array<int, int> $postIds
     */
    public static function seedNextResults(array $postIds): void
    {
        self::$nextResultIds = $postIds;
    }

    public function have_posts(): bool
    {
        return $this->cursor < count($this->resultIds);
    }

    public function the_post(): void
    {
        $postId = $this->resultIds[$this->cursor];
        $this->cursor++;

        $post = new WP_Post();
        $post->ID = $postId;
        $this->post = $post;
    }
}

function get_option(string $option, mixed $default = false): mixed
{
    return [
        'rtv_rm_options' => [
            'default_map_height' => '480px',
        ],
    ][$option] ?? $default;
}

function plugins_url(string $path = '', string $plugin = ''): string
{
    return 'https://example.com/wp-content/plugins/rubicon-maps' . $path;
}

function rest_url(string $path = ''): string
{
    return 'https://example.com/wp-json/' . ltrim($path, '/');
}

function wp_register_style(string $handle, string $src, array $deps = [], string $ver = ''): void
{
}

function wp_register_script(string $handle, string $src, array $deps = [], string $ver = '', bool $inFooter = false): void
{
}

function wp_enqueue_style(string $handle, string $src = '', array $deps = [], string $ver = ''): void
{
}

function wp_enqueue_script(string $handle, string $src = '', array $deps = [], string $ver = '', bool $inFooter = false): void
{
}

function wp_localize_script(string $handle, string $objectName, array $l10n): void
{
}

function wp_reset_postdata(): void
{
}

function get_post(int $postId): ?WP_Post
{
    $data = rtv_rm_test_post_data($postId);

    if ([] === $data) {
        return null;
    }

    $post = new WP_Post();
    $post->ID = $postId;
    $post->post_excerpt = (string) ($data['post_excerpt'] ?? '');
    $post->post_content = (string) ($data['post_content'] ?? '');

    return $post;
}

function get_post_field(string $field, int $postId): string
{
    $data = rtv_rm_test_post_data($postId);

    return (string) ($data[$field] ?? '');
}

function get_post_meta(int $postId, string $key, bool $single = false): string
{
    $data = rtv_rm_test_post_data($postId);

    return (string) ($data['meta'][$key] ?? '');
}

function get_the_title(int $postId): string
{
    $data = rtv_rm_test_post_data($postId);

    return (string) ($data['title'] ?? '');
}

function get_permalink(int $postId): string
{
    $data = rtv_rm_test_post_data($postId);

    return (string) ($data['permalink'] ?? '');
}

function get_the_post_thumbnail_url(int $postId, string $size = 'thumbnail'): string|false
{
    $data = rtv_rm_test_post_data($postId);

    return $data['image_url'] ?? false;
}

function get_the_terms(int $postId, string $taxonomy): array
{
    return [];
}

function is_wp_error(mixed $thing): bool
{
    return false;
}

function get_term_meta(int $termId, string $key, bool $single = false): string
{
    return '';
}

function wp_strip_all_tags(string $value): string
{
    return $value;
}

function wp_trim_words(string $value, int $numWords = 55): string
{
    return $value;
}

function apply_filters(string $tag, mixed $value): mixed
{
    return $value;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use RubiconMaps\Frontend\LocationListRenderer;

function assertRendererContains(string $needle, string $haystack, string $message): void
{
    if (!str_contains($haystack, $needle)) {
        fwrite(STDERR, $message . PHP_EOL);
        exit(1);
    }
}

$renderer = new LocationListRenderer();

$syncedMarkup = $renderer->render([
    'id' => 'rtv_map_synced',
    'sync_id' => 'rtv_map_synced',
    'use_fixed_height' => 'on',
    'height' => '420px',
]);

assertRendererContains('data-sync-mode="follow-map"', $syncedMarkup, 'Synced lists should render in follow-map mode.');
assertRendererContains('data-sync-id="rtv_map_synced"', $syncedMarkup, 'Synced lists should expose the saved sync ID.');
assertRendererContains('rubicon-location-list--scrollable', $syncedMarkup, 'Fixed-height synced lists should include the scrollable modifier class.');
assertRendererContains('style="height:420px"', $syncedMarkup, 'Explicit list heights should be preserved.');

$standaloneMarkup = $renderer->render([
    'id' => 'rtv_map_standalone',
    'use_fixed_height' => 'on',
    'height' => '',
]);

assertRendererContains('data-sync-mode="standalone"', $standaloneMarkup, 'Standalone lists should remain in standalone mode.');
assertRendererContains('data-default-height="480px"', $standaloneMarkup, 'Standalone lists should expose the plugin default height for runtime fallbacks.');
assertRendererContains('style="height:480px"', $standaloneMarkup, 'Standalone fixed-height lists without a saved height should fall back to the plugin default map height.');

$emptyStandaloneMarkup = $renderer->render([
    'id' => 'rtv_map_empty',
]);

assertRendererContains('data-state="empty"', $emptyStandaloneMarkup, 'Standalone lists with no matches should carry data-state="empty".');
assertRendererContains('aria-live="polite"', $emptyStandaloneMarkup, 'The list container should include an aria-live status node.');

assertRendererContains('data-state="loading"', $syncedMarkup, 'Synced lists should carry data-state="loading" initially.');

rtv_rm_test_reset_posts();
rtv_rm_test_seed_post(42, [
    'title' => 'Test Location',
    'permalink' => 'https://example.com/locations/test-location',
    'image_url' => 'https://example.com/photo.jpg',
    'meta' => [
        'latitude' => '30.0',
        'longitude' => '-97.0',
    ],
]);

WP_Query::seedNextResults([42]);

$readyMarkup = $renderer->render([
    'id' => 'rtv_map_ready',
    'show_thumbnail' => 'on',
]);

rtv_rm_test_reset_posts();

assertRendererContains('data-state="ready"', $readyMarkup, 'Standalone lists with results should carry data-state="ready".');
assertRendererContains('rubicon-location-list__thumb', $readyMarkup, 'render() should pass show_thumbnail through to list items when image_url is present.');

echo 'LocationListRendererTest passed.' . PHP_EOL;
