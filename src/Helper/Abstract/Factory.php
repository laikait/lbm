<?php

/**
 * Cloud Bill Master
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This Project Don't Provide Any Permission to Use it In Any Other Webapplication
 */

declare(strict_types=1);

// Namespace
namespace CBM\Helper\Abstract;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403) . die('403 Direct Access Denied!');

abstract class Factory
{
    abstract public static function get(): array;
    abstract public static function update(array $data, array $where): int;

    // Status Color And Format Date
    public static function colorAndDate(array $data, object $model): array
    {
        if (empty($data)) {
            return [];
        }
        if (isset($data['status'])) {
            $status = $model->first(['status' => $data['status']]);
            $data['status_color'] = $status['color'] ?? '#000000';
            $data['created'] = apply_filter('showdate', $data['created']);
            if (isset($data['updated'])) {
                $data['updated'] = apply_filter('showdate', $data['updated']);
            }
            return $data;
        } else {
            foreach ($data as $key => $val) {
                $status = $model->first(['status' => $val['status']]);
                $data[$key]['status_color'] = $status['color'] ?? '#000000';
                $data[$key]['created'] = apply_filter('showdate', $val['created']);
                if (isset($val['updated'])) {
                    $data[$key]['created'] = apply_filter('showdate', $val['updated']);
                }
            }
            return $data;
        }
    }
}