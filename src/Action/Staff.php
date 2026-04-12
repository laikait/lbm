<?php
/**
 * Laika Bill Master
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This Project Don't Provide Any Permission to Use it In Any Other Webapplication
 */

declare(strict_types=1);

namespace LBM\Action;

use Laika\Core\Http\Request;
use Laika\Core\Http\Response;
use Laika\App\Model\StaffModel;
use Laika\App\Model\StaffRoleModel;
use Laika\App\Model\StaffStatusModel;
use LBM\Exception\ActionException;

class Staff
{
    /** @var Request $request */
    protected Request $request;

    /** @var Response $response */
    protected Response $response;

    /** @var StaffModel $model */
    protected StaffModel $model;

    /** @var StaffRoleModel $role_model */
    protected StaffRoleModel $role_model;

    /** @var StaffStatusModel $status_model */
    protected StaffStatusModel $status_model;

    /** @var string $timezone */
    protected string $timezone;

    /** @var string $timeformat */
    protected string $timeformat;

    public function __construct(?Request $request = null, ?Response $response = null)
    {
        $this->request = empty($request) ? new Request() : $request;
        $this->response = empty($response) ? new Response() : $response;
        $this->model = new StaffModel();
        $this->role_model = new StaffRoleModel();
        $this->status_model = new StaffStatusModel();
        $this->timezone = do_hook('option', 'time.zone', 'UTC');
        $this->timeformat = do_hook('option', 'datetime.format', 'Y-M-d H:i:s');
    }

    /**
     * Get Staffs By Page Number
     * @return array
     */
    public function limit(): array
    {
        $columns = ['sid', 'first_name', 'middle_name', 'last_name', 'username', 'email', 'last_login_at', 'status_name', 'status_color', 'created_at'];
        $staffs = $this->model
                ->select($columns)
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->where($this->queries(), '=', 'OR')
                ->offset($this->request->input('page', 1))
                ->limit(do_hook('option.int', 'data.limit', 20))
                ->get();

        // Set DateTime Format
        foreach ($staffs as $k => $staff) {
            $staffs[$k]['created_at'] = do_hook('time.local.format', $staff['created_at'], $this->timeformat, $this->timezone);
        }
        return $staffs;
    }

    /**
     * Get Single Staff From id/Email/Username
     * @param int|string $entity Staff Entity. Example: id,username,email
     * @param array $columns Columns to Get
     * @return array
     */
    public function single(int|string $entity, array $columns)
    {
        // Throw Error If Empty Column(s) Given
        if (empty($columns)) {
            throw new ActionException("Invalid Column(s) In " . __METHOD__);
        }

        $where = [
            'sid' => $entity,
            'username' => $entity,
            'email' => $entity,
        ];

        $this->model = $this->model->select($columns);
        // Join Roles if Exists
        if (in_array('role_relid', $columns) || in_array('role_id', $columns) || in_array('role_name', $columns)
        ) {
            $this->model = $this->model->join($this->role_model->table, 'role_relid', '=', $this->role_model->id);
        }
        // Join Statuses if Exists
        if (in_array('status_relid', $columns) || in_array('status_id', $columns) || in_array('status_name', $columns)
        ) {
            $this->model = $this->model->join($this->status_model->table, 'status_relid', '=', $this->status_model->id);
        }

        $staff = $this->model->where($where, '=', 'OR')
                    ->first();
        // Convert Timestamps to Local
        if (isset($staff['created_at'])) $staff['created_at'] = do_hook('time.local.format', $staff['created_at'], $this->timeformat, $this->timezone);
        if (isset($staff['last_login_at'])) $staff['last_login_at'] = do_hook('time.local.format', $staff['last_login_at'], $this->timeformat, $this->timezone);
        if (isset($staff['updated_at'])) $staff['updated_at'] = do_hook('time.local.format', $staff['updated_at'], $this->timeformat, $this->timezone);
        if (isset($staff['deleted_at'])) $staff['deleted_at'] = do_hook('time.local.format', $staff['deleted_at'], $this->timeformat, $this->timezone);
        if (isset($staff['role_created_at'])) $staff['role_created_at'] = do_hook('time.local.format', $staff['role_created_at'], $this->timeformat, $this->timezone);
        if (isset($staff['role_updated_at'])) $staff['role_updated_at'] = do_hook('time.local.format', $staff['role_updated_at'], $this->timeformat, $this->timezone);

        return $staff;
    }

    /**
     * Update Single Staff
     * @return ?array
     */
    public function update_staff()
    {
        if ($this->request->isPost()) {
            return ['status' => true, 'message' => 'Success'];
        }
        return null;
    }

    /**
     * Count Staffs
     * @return int
     */
    public function count(): int
    {
        return $this->model
                    ->select($this->model->id)
                    ->join($this->role_model->table, 'role_relid', '=', $this->model->id)
                    ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                    ->where($this->queries(), '=', 'OR')
                    ->count();
    }

    /**
     * Get Accepted Queries
     * @return array
     */
    public function queries(): array
    {
        $query_to_column = ['id' => 'id', 'username' => 'username', 'email' => 'email', 'fname' => 'first_name', 'lname' => 'last_name', 'status' => 'status_name', 'role' => 'role_name'];
        return get_accepted_queries($this->request->inputs(), $query_to_column);
    }
}