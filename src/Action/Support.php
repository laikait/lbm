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
use LBM\Model\StaffModel;
use LBM\Model\ClientModel;
use LBM\Model\SupportTicketModel;
use LBM\Model\SupportTicketTagModel;
use LBM\Model\SupportDepartmentModel;
use LBM\Model\SupportTicketStatusModel;
use LBM\Model\SupportTicketReplyModel;
use LBM\Model\SupportTicketPriorityModel;
use LBM\Model\SupportCannedResponseModel;
use LBM\Exception\ActionException;

class Support
{
    /** @var StaffModel $staff_model */
    protected StaffModel $staff_model;

    /** @var ClientModel $client_model */
    protected ClientModel $client_model;

    /** @var SupportTicketModel $model */
    protected SupportTicketModel $model;

    /** @var SupportTicketTagModel $tag_model */
    protected SupportTicketTagModel $tag_model;

    /** @var SupportTicketStatusModel $status_model */
    protected SupportTicketStatusModel $status_model;

    /** @var SupportDepartmentModel $dep_model */
    protected SupportDepartmentModel $dep_model;

    /** @var SupportTicketReplyModel $reply_model */
    protected SupportTicketReplyModel $reply_model;

    /** @var SupportCannedResponseModel $can_model */
    protected SupportCannedResponseModel $can_model;

    /** @var SupportTicketPriorityModel $prio_model */
    protected SupportTicketPriorityModel $prio_model;

    /** @var int $limit */
    protected int $limit;

    public function __construct()
    {
        $this->model = new SupportTicketModel();
        $this->staff_model = new StaffModel();
        $this->client_model = new ClientModel();
        $this->tag_model = new SupportTicketTagModel();
        $this->status_model = new SupportTicketStatusModel();
        $this->dep_model = new SupportDepartmentModel();
        $this->reply_model = new SupportTicketReplyModel();
        $this->can_model = new SupportCannedResponseModel();
        $this->prio_model = new SupportTicketPriorityModel();
        $this->limit = option_int('data_limit', 20);
    }

    /**
     * Get Support Tickets By Page Number
     * @param string|array|null $columns = null
     * @return array
     */
    public function limit(string|array|null $columns = null): array
    {
        $columns = $columns ?: ['ticket_id', 'ticket_number', 'dep_id', 'dep_name', 'username', 'status_name', 'status_color', 'priority_name', 'priority_color', 'ticket_created_at'];

        return $this->model
                ->select($columns)
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->join($this->dep_model->table, 'department_relid', '=', $this->dep_model->id)
                ->join($this->client_model->table, 'client_relid', '=', $this->client_model->id)
                ->join($this->prio_model->table, 'priority_relid', '=', $this->prio_model->id)
                ->where($this->queries(), '=', 'OR')
                ->page(page_number())
                ->limit($this->limit)
                ->get();
    }

    /**
     * Get Single Support Ticket From ID/Number
     * @param int|string $entity Support Ticket Entity. Example: id,number
     * @param array $columns Columns to Get
     * @return array
     */
    public function single(int|string $entity, array $columns): array
    {
        // Throw Error If Empty Column(s) Given
        if (empty($columns)) {
            throw new ActionException("Invalid Column(s) In " . __METHOD__);
        }

        $where = [
            'ticket_id' => $entity,
            'ticket_number' => $entity
        ];

        return $this->model
                ->select($columns)
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->join($this->dep_model->table, 'department_relid', '=', $this->dep_model->id)
                ->join($this->prio_model->table, 'priority_relid', '=', $this->prio_model->id)
                ->where($where, '=', 'OR')
                ->first();
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
        $query_to_column = ['id' => 'id', 'username' => 'username', 'email' => 'email', 'fname' => 'first_name', 'lname' => 'last_name', 'status' => 'status_name'];
        return get_accepted_queries(Request::inputs(), $query_to_column);
    }
}