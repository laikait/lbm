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

use Laika\Service\Request;
use LBM\Model\ClientModel;
use LBM\Exception\ActionException;
use LBM\Model\ClientNoteModel;
use LBM\Model\ClientTokenModel;
use LBM\Model\StaffStatusModel;
use LBM\Model\ClientStatusModel;
use LBM\Model\ClientContactModel;
use LBM\Model\ClientServiceModel;
use LBM\Model\ClientServiceNoteModel;
use LBM\Model\ClientServiceAddonModel;
use LBM\Model\ClientServiceStatusModel;
use LBM\Model\ClientServiceConfigValueModel;

class ClientContact
{
    /** @var ClientModel $model */
    protected ClientModel $model;

    /** @var ClientStatusModel $status_model */
    protected ClientStatusModel $status_model;

    /** @var ClientNoteModel $note_model */
    protected ClientNoteModel $note_model;

    /** @var int $limit */
    protected int $limit;

    public function __construct()
    {
        $this->model = new ClientModel();
        $this->status_model = new ClientStatusModel();
        $this->note_model = new ClientNoteModel();
        $this->limit = option_int('data_limit', 20);
    }

    /**
     * Get Clients By Page Number
     * @param string|array|null $columns Default is null
     * @return array
     */
    public function limit(string|array|null $columns = null): array
    {
        $columns = $columns ?: ['id', 'company_name', 'first_name', 'middle_name', 'last_name', 'username', 'email', 'phone_cc', 'phone_number', 'status_name', 'status_color', 'created_at'];

        return $this->model
                ->select($columns)
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->where($this->queries(), '=', 'OR')
                ->page(page_number())
                ->limit($this->limit)
                ->get();
    }

    /**
     * Get Single Client From id/Email/Username
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
            'id' => $entity,
            'username' => $entity,
            'email' => $entity,
        ];

        $model = $this->model->select($columns);
        // Join Statuses if Exists
        if (in_array('status_id', $columns) || in_array('status_name', $columns) || in_array('status_color', $columns)) {
            $model = $this->model->join($this->status_model->table, 'status_relid', '=', $this->status_model->id);
        }

        return $model->where($where, '=', 'OR')->first();
    }

    /**
     * Update Single Client
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
        $query_to_column = ['cid' => 'id', 'username' => 'username', 'email' => 'email', 'fname' => 'first_name', 'lname' => 'last_name', 'status' => 'status_name'];
        return get_accepted_queries(Request::inputs(), $query_to_column);
    }
}