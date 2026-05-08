<?php
/**
 * Laika Bill Master
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
        $this->limit = option_int('data.limit', 20);
        $this->acceptedQueries = [];
    }

    /**
     * @return ?array
     */
    public function create(): ?array
    {
        return ['status' => true, 'message' => 'Address Created Successfully!'];
    }

    /**
     * @param array $data Data to Update Address
     * @return ?array
     */
    public function update(array $data): ?array
    {
        return ['status' => true, 'message' => 'Address Updated Successfully!'];
    }
}