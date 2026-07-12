<?php

namespace RubiconMaps\Frontend;

use RubiconMaps\Helpers\SettingsHelper;

use function esc_attr;
use function esc_html;
use function esc_url;
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
     * Render a linked location list for shortcodes or Divi modules.
     *
     * @param array<string, mixed> $atts List instance attributes.
     */
    public function render(array $atts = []): string
    {
        FrontendAssetManager::enqueueList();

        $instanceId = trim((string) ($atts['id'] ?? ''));
        $syncId = trim((string) ($atts['sync_id'] ?? ''));
        $isSynced = '' !== $syncId;
        $fixedHeightEnabled = $this->isEnabled($atts['use_fixed_height'] ?? '');
        $savedHeight = trim((string) ($atts['height'] ?? ''));
        $defaultHeight = trim((string) ($atts['default_height'] ?? SettingsHelper::get_option('default_map_height', '480px')));
        $listHeight = $savedHeight;
        $showThumbnail = $this->isEnabled($atts['show_thumbnail'] ?? '');

        if ('' === $instanceId) {
            $instanceId = 'rubicon-map-' . wp_unique_id();
        }

        if ($fixedHeightEnabled && '' === $listHeight && !$isSynced) {
            $listHeight = $defaultHeight;
        }

        $locations = $isSynced
            ? []
            : $this->getRepository()->getLocations([
                'category' => $atts['category'] ?? '',
                'region' => $atts['region'] ?? '',
                'location_ids' => $atts['location_ids'] ?? '',
                'posts_per_page' => $atts['posts_per_page'] ?? -1,
            ]);

        $isEmpty = !$isSynced && [] === $locations;
        $state = $isSynced ? 'loading' : ($isEmpty ? 'empty' : 'ready');
        $strings = FrontendAssetManager::frontendStrings();

        ob_start();
        ?>
        <div
            class="rubicon-location-list<?php echo $fixedHeightEnabled ? ' rubicon-location-list--scrollable' : ''; ?>"
            data-rubicon-location-list="1"
            data-instance-id="<?php echo esc_attr($instanceId); ?>"
            data-sync-id="<?php echo esc_attr($syncId); ?>"
            data-sync-mode="<?php echo esc_attr($isSynced ? 'follow-map' : 'standalone'); ?>"
            data-state="<?php echo esc_attr($state); ?>"
            data-use-fixed-height="<?php echo esc_attr($fixedHeightEnabled ? '1' : '0'); ?>"
            data-height="<?php echo esc_attr($savedHeight); ?>"
            data-default-height="<?php echo esc_attr($defaultHeight); ?>"
            <?php if ($fixedHeightEnabled && '' !== $listHeight) : ?>
                style="height:<?php echo esc_attr($listHeight); ?>"
            <?php endif; ?>
        >
            <div class="rubicon-location-list__status" aria-live="polite"></div>
            <?php if ($isEmpty) : ?>
                <p class="rubicon-location-list__empty"><?php echo esc_html($strings['emptyStandalone']); ?></p>
            <?php else : ?>
                <ul class="rubicon-location-list__items" role="list">
                    <?php foreach ($locations as $location) : ?>
                        <?php echo $this->renderListItem($location, $showThumbnail); ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php

        return (string) ob_get_clean();
    }

    /**
     * @param array<string, mixed> $location
     */
    private function renderListItem(array $location, bool $showThumbnail = false): string
    {
        ob_start();
        ?>
        <li
            class="rubicon-location-list__item"
            data-location-id="<?php echo esc_attr((string) $location['id']); ?>"
            data-lat="<?php echo esc_attr((string) $location['latitude']); ?>"
            data-lng="<?php echo esc_attr((string) $location['longitude']); ?>"
            tabindex="0"
            role="button"
            aria-label="<?php echo esc_attr(sprintf(__('Focus map on %s', 'rubicon-maps'), $location['title'])); ?>"
        >
            <?php if ($showThumbnail && !empty($location['image_url'])) : ?>
                <img class="rubicon-location-list__thumb" src="<?php echo esc_url((string) $location['image_url']); ?>" alt="">
            <?php endif; ?>
            <strong class="rubicon-location-list__title"><?php echo esc_html((string) $location['title']); ?></strong>
            <?php if (!empty($location['formatted_address'])) : ?>
                <div class="rubicon-location-list__meta"><?php echo esc_html((string) $location['formatted_address']); ?></div>
            <?php endif; ?>
            <?php if (!empty($location['excerpt'])) : ?>
                <div class="rubicon-location-list__excerpt"><?php echo wp_kses_post((string) $location['excerpt']); ?></div>
            <?php endif; ?>
        </li>
        <?php

        return (string) ob_get_clean();
    }

    private function isEnabled(mixed $value): bool
    {
        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'on', 'yes'], true);
    }

    private function getRepository(): LocationRepository
    {
        return $this->repository ?? new LocationRepository();
    }
}
