<?php
// SPDX-License-Identifier: GPL-2.0-or-later

namespace RubiconMaps\Admin;

use RubiconMaps\ImportExport\LocationCsvExporter;
use RubiconMaps\ImportExport\LocationCsvImporter;
use RubiconMaps\ImportExport\LocationCsvTransformer;
use RubiconMaps\Support\Plugin;

use function add_query_arg;
use function admin_url;
use function check_admin_referer;
use function current_user_can;
use function esc_attr;
use function esc_html;
use function esc_html__;
use function esc_url;
use function sanitize_key;
use function sprintf;
use function wp_die;
use function wp_nonce_field;
use function wp_nonce_url;
use function wp_safe_redirect;

if (!defined('ABSPATH')) {
    exit;
}

final class ImportExportPage
{
    public const PAGE_SLUG = 'rubicon-maps-import-export';
    private const NONCE_EXPORT = 'rubicon_maps_export_locations';
    private const NONCE_IMPORT = 'rubicon_maps_import_locations';
    private const NONCE_TEMPLATE = 'rubicon_maps_download_template';

    public static function init(): void
    {
        add_action('admin_menu', [self::class, 'add_submenu_page']);
        add_action('admin_post_rubicon_maps_export_locations', [self::class, 'handle_export']);
        add_action('admin_post_rubicon_maps_import_locations', [self::class, 'handle_import']);
        add_action('admin_post_rubicon_maps_download_csv_template', [self::class, 'handle_template_download']);
    }

    public static function add_submenu_page(): void
    {
        add_submenu_page(
            Plugin::MENU_PAGE_SLUG,
            __('Import / Export', 'rubicon-maps'),
            __('Import / Export', 'rubicon-maps'),
            'manage_options',
            self::PAGE_SLUG,
            [self::class, 'render']
        );
    }

    public static function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $notice = sanitize_key((string) ($_GET['rubicon_notice'] ?? ''));
        $created = absint($_GET['created'] ?? 0);
        $updated = absint($_GET['updated'] ?? 0);
        $skipped = absint($_GET['skipped'] ?? 0);
        $warnings = absint($_GET['warnings'] ?? 0);
        $errorCount = absint($_GET['errors'] ?? 0);
        ?>
        <div class="wrap rubicon-admin rubicon-admin--import-export">
            <h1><?php esc_html_e('Rubicon Maps Import / Export', 'rubicon-maps'); ?></h1>
            <p><?php esc_html_e('Move locations between environments or bulk update your directory with the canonical Rubicon Maps CSV format.', 'rubicon-maps'); ?></p>

            <?php if ('import-success' === $notice) : ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <?php
                        echo esc_html(
                            sprintf(
                                /* translators: 1: created count, 2: updated count, 3: skipped count, 4: warning count, 5: error count */
                                __('Import complete. Created: %1$d. Updated: %2$d. Skipped: %3$d. Warnings: %4$d. Errors: %5$d.', 'rubicon-maps'),
                                $created,
                                $updated,
                                $skipped,
                                $warnings,
                                $errorCount
                            )
                        );
                        ?>
                    </p>
                </div>
            <?php elseif ('import-failed' === $notice) : ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php esc_html_e('The CSV import could not be completed. Check the selected file and try again.', 'rubicon-maps'); ?></p>
                </div>
            <?php endif; ?>

            <div class="card" style="max-width:960px;padding:24px;">
                <h2><?php esc_html_e('Export Locations', 'rubicon-maps'); ?></h2>
                <p><?php esc_html_e('Download every Rubicon Maps location as a CSV file using the canonical import/export schema.', 'rubicon-maps'); ?></p>
                <p>
                    <a class="button button-primary" href="<?php echo esc_url(self::exportUrl()); ?>">
                        <?php esc_html_e('Download Locations CSV', 'rubicon-maps'); ?>
                    </a>
                </p>
            </div>

            <div class="card" style="max-width:960px;padding:24px;margin-top:18px;">
                <h2><?php esc_html_e('Import Locations', 'rubicon-maps'); ?></h2>
                <p><?php esc_html_e('Upload a CSV with Rubicon Maps headers. Existing locations are updated when the CSV includes a matching location ID.', 'rubicon-maps'); ?></p>
                <p>
                    <a class="button button-secondary" href="<?php echo esc_url(self::templateUrl()); ?>">
                        <?php esc_html_e('Download CSV Template', 'rubicon-maps'); ?>
                    </a>
                </p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="rubicon_maps_import_locations" />
                    <?php wp_nonce_field(self::NONCE_IMPORT); ?>
                    <input type="file" name="rubicon_maps_csv" accept=".csv,text/csv" required />
                    <p class="description"><?php esc_html_e('Required columns: title, excerpt, content, street, city, state, zip, country, latitude, longitude, phone, email, website, categories, regions, featured_image, marker_icon_id, menu_order, status.', 'rubicon-maps'); ?></p>
                    <p>
                        <button type="submit" class="button button-primary"><?php esc_html_e('Import CSV', 'rubicon-maps'); ?></button>
                    </p>
                </form>
            </div>
        </div>
        <?php
    }

    public static function handle_export(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You are not allowed to export Rubicon Maps locations.', 'rubicon-maps'));
        }

        check_admin_referer(self::NONCE_EXPORT);

        (new LocationCsvExporter())->download();
        exit;
    }

    public static function handle_template_download(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You are not allowed to download the Rubicon Maps CSV template.', 'rubicon-maps'));
        }

        check_admin_referer(self::NONCE_TEMPLATE);

        $stream = fopen('php://output', 'wb');

        if (false === $stream) {
            wp_die(esc_html__('The CSV template could not be generated.', 'rubicon-maps'));
        }

        nocache_headers();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="rubicon-maps-template.csv"');

        fputcsv($stream, LocationCsvTransformer::headers());
        fputcsv($stream, LocationCsvTransformer::templateRow());

        fclose($stream);
        exit;
    }

    public static function handle_import(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You are not allowed to import Rubicon Maps locations.', 'rubicon-maps'));
        }

        check_admin_referer(self::NONCE_IMPORT);

        $file = $_FILES['rubicon_maps_csv']['tmp_name'] ?? '';

        if (!is_string($file) || '' === $file) {
            wp_safe_redirect(self::redirectUrl('import-failed'));
            exit;
        }

        $summary = (new LocationCsvImporter())->importFromFile($file);

        wp_safe_redirect(
            add_query_arg(
                [
                    'page' => self::PAGE_SLUG,
                    'rubicon_notice' => 'import-success',
                    'created' => $summary['created'],
                    'updated' => $summary['updated'],
                    'skipped' => $summary['skipped'],
                    'warnings' => $summary['warnings'],
                    'errors' => count($summary['errors']),
                ],
                admin_url('admin.php')
            )
        );
        exit;
    }

    private static function exportUrl(): string
    {
        return wp_nonce_url(
            admin_url('admin-post.php?action=rubicon_maps_export_locations'),
            self::NONCE_EXPORT
        );
    }

    private static function templateUrl(): string
    {
        return wp_nonce_url(
            admin_url('admin-post.php?action=rubicon_maps_download_csv_template'),
            self::NONCE_TEMPLATE
        );
    }

    private static function redirectUrl(string $notice): string
    {
        return add_query_arg(
            [
                'page' => self::PAGE_SLUG,
                'rubicon_notice' => $notice,
            ],
            admin_url('admin.php')
        );
    }
}
