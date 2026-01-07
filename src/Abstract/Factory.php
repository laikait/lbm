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
namespace LBM\Abstract;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403) . die('403 Direct Access Denied!');

abstract class Factory
{
    /**
     * Get Row by Request
     * @param int|string $entity Entity to Get Value.
     * @param ?string $columns Columns for Select Query. Default is null.
     * @return array
     */
    abstract public static function first(int|string $entity, ?string $columns = null): array;

    /**
     * Get Rows by Request
     * @param ?string $columns Columns for Select Query. Default is null.
     * @return array
     */
    abstract public static function get(?string $columns = null): array;

    /**
     * Search Rows by Request
     * @param ?string $columns Columns for Select Query. Default is null.
     * @return array
     */
    abstract public static function search(?string $columns = null): array;

    /**
     * @param array $where
     * @param array $data Data to Update
     */
    abstract public static function update(array $where, array $data): int;
}