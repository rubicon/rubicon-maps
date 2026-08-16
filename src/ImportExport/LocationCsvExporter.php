<?php
// SPDX-License-Identifier: GPL-2.0-or-later

namespace RubiconMaps\ImportExport;

use RubiconMaps\Frontend\LocationRepository;
use RubiconMaps\Support\Plugin;

use function current_time;
use function fputcsv;
use function get_post_field;
use function header;
use function nocache_headers;
use function wp_die;

if (!defined('ABSPATH')) {
    exit;
}

final class LocationCsvExporter
{
    public function __construct(private readonly ?LocationRepository $repository = null)
    {
    }

    public function download(): void
    {
        $filename = sprintf('rubicon-maps-locations-%s.csv', current_time('Y-m-d'));

        nocache_headers();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $stream = fopen('php://output', 'wb');

        if (false === $stream) {
            wp_die(esc_html__('Unable to open the CSV export stream.', 'rubicon-maps'));
        }

        fputcsv($stream, LocationCsvTransformer::headers());

        foreach ($this->getRepository()->getLocations(['posts_per_page' => -1]) as $location) {
            $row = LocationCsvTransformer::buildExportRow(
                array_merge(
                    $location,
                    [
                        'menu_order' => (int) get_post_field('menu_order', (int) $location['id']),
                        'status' => (string) get_post_field('post_status', (int) $location['id']),
                    ]
                )
            );

            fputcsv($stream, $row);
        }

        fclose($stream);
    }

    private function getRepository(): LocationRepository
    {
        return $this->repository ?? new LocationRepository();
    }
}
