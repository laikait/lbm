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
     * @return array
     */
    public function single(int|string $staff): array
    {
        $staff = htmlspecialchars($staff);
        $where = [
            'id'        =>  $staff,
            'uuid'      =>  $staff,
            'username'  =>  $staff,
            'email'     =>  $staff
        ];
        return $this->model->row($where, '=', 'OR')->status()->role()->result();
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