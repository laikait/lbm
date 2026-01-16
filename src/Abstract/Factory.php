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
     * @return array
     */
    abstract public function first(int|string $entity): array;

    /**
     * Get Rows by Request
     * @return array
     */
    abstract public function limit(): array;
}