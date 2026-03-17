<?php

namespace RubiconMaps\Divi5\Modules\RubiconLocationListModule;

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Module;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;
use ET\Builder\Packages\Module\Options\Element\ElementClassnames;
use RubiconMaps\Frontend\LocationListRenderer;

if (!defined('ABSPATH')) {
    exit;
}

final class RubiconLocationListModule implements DependencyInterface
{
    public function load(): void
    {
        $moduleJsonFolderPath = dirname(__DIR__, 3) . '/visual-builder/src/modules/rubicon-location-list';

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
        $markup = (new LocationListRenderer())->render(
            [
                'id' => self::textAttr($attrs, 'instanceId', self::generatedSyncId($block)),
                'category' => self::textAttr($attrs, 'category'),
                'region' => self::textAttr($attrs, 'region'),
                'location_ids' => self::textAttr($attrs, 'locationIds'),
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

    private static function generatedSyncId(object $block): string
    {
        $rawId = (string) ($block->parsed_block['id'] ?? '');
        $normalized = substr(md5($rawId), 0, 6);

        return 'rtv_map_' . $normalized;
    }
}
