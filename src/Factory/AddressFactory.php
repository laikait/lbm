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
namespace LBM\Factory;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403) . die('403 Direct Access Denied!');

use Laika\Core\Http\Request;
use Laika\App\Model\Address;
use LBM\Abstract\Factory;

class AddressFactory extends Factory
{
    /**
     * Initiate Factory
     */
    public function __construct()
    {
        $this->model = new Address();
        $this->page = (int) \call_user_func([new Request, 'input'], 'page', 1);
        $this->limit = \do_hook('option.int', 'data.limit', 20);
        $this->acceptedQueries = [];
    }

    /**
     * @param int|string $entity
     * @return array
     */
    public function first(int|string $entity): array
    {
        return [];
    }

    /**
     * Get Limit Address
     */
    public function limit(): array
    {
        return [];
    }
}