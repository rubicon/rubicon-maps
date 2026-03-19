<?php

namespace RubiconMaps\Divi5\Modules\RubiconMapModule;

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Module;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;
use ET\Builder\Packages\Module\Options\Element\ElementClassnames;
use RubiconMaps\Frontend\MapRenderer;

if (!defined('ABSPATH')) {
    exit;
}

final class RubiconMapModule implements DependencyInterface
{
    public function load(): void
    {
        $moduleJsonFolderPath = dirname(__DIR__, 3) . '/visual-builder/src/modules/rubicon-map';

        add_action(
            'init',
            static function () use ($moduleJsonFolderPath): void {
                ModuleRegistration::register_module(
                    $moduleJsonFolderPath,
                    [
                        'render_callback' => [self::class, 'renderCallback'],
                    ]
                );
            }
        );
    }

    public static function renderCallback($attrs, $content, $block, $elements): string
    {
        $markup = (new MapRenderer())->render(
            [
                'id' => self::textAttr($attrs, 'instanceId', self::generatedRuntimeId($block)),
                'sync_id' => self::textAttr($attrs, 'instanceId'),
                'provider' => self::textAttr($attrs, 'provider'),
                'category' => self::textAttr($attrs, 'category'),
                'region' => self::textAttr($attrs, 'region'),
                'location_ids' => self::textAttr($attrs, 'locationIds'),
                'viewport_mode' => self::textAttr($attrs, 'viewportMode'),
                'lat' => self::textAttr($attrs, 'latitude'),
                'lng' => self::textAttr($attrs, 'longitude'),
                'zoom' => self::textAttr($attrs, 'zoom'),
                'height' => self::textAttr($attrs, 'height'),
                'auto_fit_padding' => self::textAttr($attrs, 'autoFitPadding'),
                'tile_preset' => self::textAttr($attrs, 'tilePreset'),
                'zoom_control' => self::textAttr($attrs, 'zoomControl'),
                'scrollwheel' => self::textAttr($attrs, 'scrollWheelZoom'),
                'double_click_zoom' => self::textAttr($attrs, 'doubleClickZoom'),
                'popup_trigger' => self::textAttr($attrs, 'popupTrigger'),
                'popup_max_width' => self::textAttr($attrs, 'popupMaxWidth'),
                'close_on_map_click' => self::textAttr($attrs, 'closeOnMapClick'),
                'auto_close_popup' => self::textAttr($attrs, 'autoClosePopup'),
                'open_all_popups' => self::textAttr($attrs, 'openAllPopups'),
                'enable_clustering' => self::textAttr($attrs, 'enableClustering'),
                'cluster_radius' => self::textAttr($attrs, 'clusterRadius'),
            ]
        );

        $parent = BlockParserStore::get_parent($block->parsed_block['id'], $block->parsed_block['storeInstance']);

        return Module::render(
            [
                'orderIndex' => $block->parsed_block['orderIndex'],
                'storeInstance' => $block->parsed_block['storeInstance'],
                'attrs' => $attrs,
                'elements' => $elements,
                'id' => $block->parsed_block['id'],
                'moduleClassName' => '',
                'name' => $block->block_type->name,
                'classnamesFunction' => [self::class, 'moduleClassnames'],
                'moduleCategory' => $block->block_type->category,
                'stylesComponent' => [self::class, 'moduleStyles'],
                'scriptDataComponent' => [self::class, 'moduleScriptData'],
                'parentAttrs' => $parent->attrs ?? [],
                'parentId' => $parent->id ?? '',
                'parentName' => $parent->blockName ?? '',
                'children' => $elements->style_components(
                    [
                        'attrName' => 'module',
                    ]
                ) . $markup,
            ]
        );
    }

    public static function moduleClassnames(array $args): void
    {
        $classnamesInstance = $args['classnamesInstance'];
        $attrs = $args['attrs'];

        $classnamesInstance->add(
            ElementClassnames::classnames(
                [
                    'attrs' => array_merge(
                        $attrs['module']['decoration'] ?? [],
                        [
                            'link' => $attrs['module']['advanced']['link'] ?? [],
                        ]
                    ),
                ]
            )
        );
    }

    public static function moduleStyles(array $args): void
    {
        $elements = $args['elements'];
        $settings = $args['settings'] ?? [];

        Style::add(
            [
                'id' => $args['id'],
                'name' => $args['name'],
                'orderIndex' => $args['orderIndex'],
                'storeInstance' => $args['storeInstance'],
                'styles' => [
                    $elements->style(
                        [
                            'attrName' => 'module',
                            'styleProps' => [
                                'disabledOn' => [
                                    'disabledModuleVisibility' => $settings['disabledModuleVisibility'] ?? null,
                                ],
                            ],
                        ]
                    ),
                ],
            ]
        );
    }

    public static function moduleScriptData(array $args): void
    {
        $args['elements']->script_data(
            [
                'attrName' => 'module',
            ]
        );
    }

    private static function textAttr(array $attrs, string $key, string $fallback = ''): string
    {
        $value = $attrs[$key]['innerContent']['desktop']['value']
            ?? $attrs[$key]['innerContent']
            ?? $attrs[$key]
            ?? $fallback;

        return is_scalar($value) ? trim((string) $value) : $fallback;
    }

    private static function generatedRuntimeId(object $block): string
    {
        $rawId = (string) ($block->parsed_block['id'] ?? '');
        $normalized = substr(md5($rawId), 0, 6);

        return 'rtv_map_' . $normalized;
    }
}
