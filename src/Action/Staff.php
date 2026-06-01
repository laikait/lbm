<?php
/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This Project Don't Provide Any Permission to Use it In Any Other Webapplication
 */

declare(strict_types=1);

namespace LBM\Action;

use Laika\Core\Service\Request;
use App\Model\StaffModel;
use App\Model\StaffRoleModel;
use App\Model\StaffStatusModel;
use LBM\Exception\ActionException;

class Staff
{
    /** @var StaffModel $model */
    protected StaffModel $model;

    /** @var StaffRoleModel $role_model */
    protected StaffRoleModel $role_model;

    /** @var StaffStatusModel $status_model */
    protected StaffStatusModel $status_model;

    /** @var int $limit */
    protected int $limit;

    public function __construct()
    {
        $this->model = new StaffModel();
        $this->role_model = new StaffRoleModel();
        $this->status_model = new StaffStatusModel();
        $this->limit = option_int('data_limit', 20);
    }

    /**
     * Get Staffs By Page Number
     * @param string|array|null $columns Default is null
     * @return array
     */
    public function limit(string|array|null $columns = null): array
    {
        $columns = $columns  ?: ['sid', 'first_name', 'middle_name', 'last_name', 'username', 'email', 'last_login_at', 'status_name', 'status_color', 'created_at'];

        return $this->model
                ->select($columns)
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->where($this->queries(), '=', 'OR')
                ->page(page_number())
                ->limit($this->limit)
                ->get();
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

        $model = $this->model->select($columns);

        // Join Roles if Exists
        if (in_array('role_relid', $columns) || in_array('role_id', $columns) || in_array('role_name', $columns)
        ) {
            $model = $this->model->join($this->role_model->table, 'role_relid', '=', $this->role_model->id);
        }

        // Join Statuses if Exists
        if (in_array('status_relid', $columns) || in_array('status_id', $columns) || in_array('status_name', $columns)
        ) {
            $model = $this->model->join($this->status_model->table, 'status_relid', '=', $this->status_model->id);
        }

        return $model->where($where, '=', 'OR')->first();
    }

    /**
     * Update Single Staff
     * @param int|string $entity
     * @return ?array
     */
    public function update(int|string $entity): ?array
    {
        if (Request::isPost()) {
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
        $query_to_column = ['sid' => 'id', 'username' => 'username', 'email' => 'email', 'fname' => 'first_name', 'lname' => 'last_name', 'status' => 'status_name', 'role' => 'role_name'];
        return get_accepted_queries(Request::inputs(), $query_to_column);
    }
}