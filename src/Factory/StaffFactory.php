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

use LBM\Exception\FactoryException;
use Laika\App\Model\StaffActivity;
use Laika\App\Model\StaffStatus;
use Laika\App\Model\Staff;
use LBM\Abstract\Factory;

class StaffFactory extends Factory
{
    /**
     * Initiate Staff Factory
     */
    public function __construct()
    {
        parent::__construct('Staff', ['id', 'uid', 'username', 'email', 'fname', 'lname', 'status']);
    }

    /**
     * Create Activity
     * @return void
     */
    public function createActivity(array $data): void
    {
        try {
            $model = new StaffActivity();
            $model->transaction(function ($m) use ($data) {
                $m->insert($data);
            });
        } catch (FactoryException $th) {
            throw new FactoryException("Unable to Insert Staff Activity!");
        }
        return;
    }

    public function create(): ?array
    {
        return ['status' => true, 'message' => 'Staff Created Successfully'];
    }

    public function update(array $taff): ?array
    {
        return ['status' => true, 'message' => 'Staff Updated Successfully'];
    }
}