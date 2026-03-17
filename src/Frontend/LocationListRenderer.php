<?php

namespace RubiconMaps\Frontend;

use function esc_attr;
use function esc_html;
use function wp_kses_post;

if (!defined('ABSPATH')) {
    exit;
}

final class LocationListRenderer
{
    public function __construct(private readonly ?LocationRepository $repository = null)
    {
    }

    /**
     * @param array<string, mixed> $atts
     * @return array{instanceId: string, syncMode: string, isSynced: bool}
     */
    public static function resolveInstanceState(array $atts, string $generatedId = ''): array
    {
        $providedId = trim((string) ($atts['id'] ?? ''));
        $instanceId = '' !== $providedId ? $providedId : $generatedId;

        return [
            'instanceId' => $instanceId,
            'syncMode' => '' !== $providedId ? 'follow-map' : 'standalone',
            'isSynced' => '' !== $providedId,
        ];
    }

    /**
     * Render a linked location list for shortcodes or Divi modules.
     *
     * @param array<string, mixed> $atts List instance attributes.
     */
    public function render(array $atts = []): string
    {
        FrontendAssetManager::enqueueList();

        $instanceState = self::resolveInstanceState($atts, 'rubicon-map-' . wp_unique_id());
        $instanceId = $instanceState['instanceId'];
        $syncMode = $instanceState['syncMode'];

        $locations = 'follow-map' === $syncMode
            ? []
            : $this->getRepository()->getLocations([
                'category' => $atts['category'] ?? '',
                'region' => $atts['region'] ?? '',
                'location_ids' => $atts['location_ids'] ?? '',
                'posts_per_page' => $atts['posts_per_page'] ?? -1,
            ]);

        ob_start();
        ?>
        <div class="rubicon-location-list" data-rubicon-location-list="1" data-instance-id="<?php echo esc_attr($instanceId); ?>" data-sync-mode="<?php echo esc_attr($syncMode); ?>">
            <?php if ([] === $locations) : ?>
                <p class="rubicon-location-list__empty"><?php echo 'follow-map' === $syncMode ? esc_html__('This synced listing loads from the paired map.', 'rubicon-maps') : esc_html__('No locations matched this map instance.', 'rubicon-maps'); ?></p>
            <?php else : ?>
                <ul class="rubicon-location-list__items" role="list">
                    <?php foreach ($locations as $location) : ?>
                        <li
                            class="rubicon-location-list__item"
                            data-location-id="<?php echo esc_attr((string) $location['id']); ?>"
                            data-lat="<?php echo esc_attr((string) $location['latitude']); ?>"
                            data-lng="<?php echo esc_attr((string) $location['longitude']); ?>"
                            tabindex="0"
                            role="button"
                            aria-label="<?php echo esc_attr(sprintf(__('Focus map on %s', 'rubicon-maps'), $location['title'])); ?>"
                        >
                            <strong class="rubicon-location-list__title"><?php echo esc_html((string) $location['title']); ?></strong>
                            <?php if (!empty($location['formatted_address'])) : ?>
                                <div class="rubicon-location-list__meta"><?php echo esc_html((string) $location['formatted_address']); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($location['excerpt'])) : ?>
                                <div class="rubicon-location-list__excerpt"><?php echo wp_kses_post((string) $location['excerpt']); ?></div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php

        return (string) ob_get_clean();
    }

    private function getRepository(): LocationRepository
    {
        return $this->repository ?? new LocationRepository();
    }
}
