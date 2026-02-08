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
use Laika\App\Model\StaffRole;
use Laika\Core\Http\Request;
use Laika\App\Model\Staff;
use LBM\Abstract\Factory;

class StaffFactory extends Factory
{
    /**
     * Initiate Staff Factory
     */
    public function __construct()
    {
        $this->model = new Staff();
        $this->page = (int) \call_user_func([new Request, 'input'], 'page', 1);
        $this->limit = \do_hook('option.int', 'data.limit', 20);
        $this->acceptedQueries = ['id', 'uuid', 'username', 'email', 'fname', 'lname', 'status'];
    }

    /**
     * Get Single Staff
     * @param int|string $staff Staff to Get Value. Example: id, uuid, username, email
     * @param ?string $columns Table columns. Example: 'id,uuid,username
     * @return array
     */
    public function first(int|string $staff): array
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
        // Get Input
        $input = \do_hook('request.input', 'staff');

        // Get Model Object for Total Staffs
        $total = (new Staff())->select($this->model->id);
        if (!empty($input)) {
            $input = "^{$input}";
            $where = [
                'fname' => $input,
                'lname' => $input,
                'username' => $input,
                'email' => $input,
                'status' => $input
            ];
            // Extend Total Staff Model
            $total = $total->where($where, 'REGEXP', 'OR');
            // Extend Staff Model
            $this->model = $this->model->where($where, 'REGEXP', 'OR');
        } else {
            // Extend Total Staff Model
            $total = $total->where($this->queries());
            // Extend Staff Model
            $this->model = $this->model->where($this->queries());
        }

        // Return Result
        $staffs = $this->model->select()->limit($this->limit)->offset($this->page)->get();
        // Set Total Staff
        $this->total = $total->count();

        // Get Related Values
        if (!empty($staffs)) {
            $smodel = new StaffStatus();
            foreach ($staffs as $k => $staff) {
                // Get Status
                $staffs[$k]['status'] = $smodel->select('entity,color')->where(['entity' => $staff['status']])->first();
            }
        }

        return $staffs;
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
}