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
namespace CBM\Factory;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403) . die('403 Direct Access Denied!');

use CBM\Helper\Abstract\Factory;

class ClientFactory extends Factory
{
    public static function get(): array
    {
        return [];
    }

    public static function update(array $data, array $where): int
    {
        return 1;
    }
}