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

use Laika\App\Model\StaffStatus;
use Laika\App\Model\StaffRole;
use Laika\Core\Http\Request;
use Laika\App\Model\Staff;

class StaffFactory
{
    /**
     * @var Staff $model
     */
    private Staff $model;

    /**
     * Initiate Client Factory
     */
    public function __construct()
    {
        $this->model = new Staff();
    }

    /**
     * Get Single Staff
     * @param int|string $staff Staff to Get Value. Example: id, uuid, username, email
     * @param ?string $columns Table columns. Example: 'id,uuid,username
     * @return array
     */
    public function single(int|string $staff): array
    {
        $where = [
            'id'        =>  $staff,
            'uuid'      =>  $staff,
            'username'  =>  $staff,
            'email'     =>  $staff
        ];

        // Get Staff
        $staff = $this->model->where($where, '=', 'OR')->first();

        // Get Related Values
        if (!empty($staff)) {
            // Add Status
            $staff['status'] = (new StaffStatus)->where(['entity' => $staff['status']])->first();
            // Add Role
            $role = (new StaffRole)->select('role,entities')->where(['role' => $staff['role']])->first();
            $role['entities'] = unserialize($role['entities']);
            $staff['role'] = $role;
        }
        return $staff;
    }

    /**
     * Get Limit Staffs
     */
    public function limit(): array
    {
        // Get Page Number
        $page = call_user_func([new Request, 'input'], 'page', 1);
        return $this->model->rows($this->queries(), page:$page)->status()->role()->result();
    }

    /**
     * Find Staffs
     */
    public function find(): array
    {
        // Get Page Number
        return $this->model->rows($this->queries())->status()->role()->result();
    }

    /*============================ INTERNAL API ============================*/
    /**
     * Match Database Columns with Queries
     * @return array
     */
    private function queries()
    {
        $accepted = ['role', 'fname', 'lname', 'username', 'email', 'status'];
        $queries = [];
        $inputs = call_user_func([new Request(), 'inputs']);
        // Get Accepted Query Values
        foreach($inputs as $k => $v) {
            if (in_array($k, $accepted)) {
                $queries[$k] = $v;
            }
        }
        return $queries;
    }
}