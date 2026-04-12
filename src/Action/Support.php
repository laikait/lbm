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
use Laika\App\Model\ClientModel;
use Laika\App\Model\SupportTicketModel;
use Laika\App\Model\SupportTicketTagModel;
use Laika\App\Model\SupportDepartmentModel;
use Laika\App\Model\SupportTicketStatusModel;
use Laika\App\Model\SupportTicketReplyModel;
use Laika\App\Model\SupportTicketPriorityModel;
use Laika\App\Model\SupportCannedResponseModel;
use LBM\Exception\ActionException;

class Support
{
    /** @var Request $request */
    protected Request $request;

    /** @var Response $response */
    protected Response $response;

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

    /** @var string $timezone */
    protected string $timezone;

    /** @var string $timeformat */
    protected string $timeformat;

    public function __construct(?Request $request = null, ?Response $response = null)
    {
        $this->request = empty($request) ? new Request() : $request;
        $this->response = empty($response) ? new Response() : $response;
        $this->model = new SupportTicketModel();
        $this->staff_model = new StaffModel();
        $this->client_model = new ClientModel();
        $this->tag_model = new SupportTicketTagModel();
        $this->status_model = new SupportTicketStatusModel();
        $this->dep_model = new SupportDepartmentModel();
        $this->reply_model = new SupportTicketReplyModel();
        $this->can_model = new SupportCannedResponseModel();
        $this->prio_model = new SupportTicketPriorityModel();
        $this->timezone = do_hook('option', 'time.zone', 'UTC');
        $this->timeformat = do_hook('option', 'datetime.format', 'Y-M-d H:i:s');
    }

    /**
     * Get Support Tickets By Page Number
     * @return array
     */
    public function limit(): array
    {
        $columns = ['ticket_id', 'ticket_number', 'dep_id', 'dep_name', 'username', 'status_name', 'status_color', 'priority_name', 'priority_color', 'ticket_created_at'];

        $tickets = $this->model
                ->select($columns)
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->join($this->dep_model->table, 'department_relid', '=', $this->dep_model->id)
                ->join($this->client_model->table, 'client_relid', '=', $this->client_model->id)
                ->join($this->prio_model->table, 'priority_relid', '=', $this->prio_model->id)
                ->where($this->queries(), '=', 'OR')
                ->offset($this->request->input('page', 1))
                ->limit(do_hook('option.int', 'data.limit', 20))
                ->get();

        // Set DateTime Format
        foreach ($tickets as $k => $ticket) {
            $tickets[$k]['ticket_created_at'] = do_hook('time.local.format', $ticket['ticket_created_at'], $this->timeformat, $this->timezone);
        }
        return $tickets;
    }

    /**
     * Get Single Support Ticket From ID/Number
     * @param int|string $entity Support Ticket Entity. Example: id,number
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
            'ticket_id' => $entity,
            'ticket_number' => $entity
        ];

        $ticket = $this->model
                ->select($columns)
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->join($this->dep_model->table, 'department_relid', '=', $this->dep_model->id)
                ->join($this->prio_model->table, 'priority_relid', '=', $this->prio_model->id)
                ->where($where, '=', 'OR')
                ->first();

        // Convert Timestamps to Local
        if (isset($ticket['last_reply_at'])) $ticket['last_reply_at'] = do_hook('time.local.format', $ticket['last_reply_at'], $this->timeformat, $this->timezone);
        if (isset($ticket['created_at'])) $ticket['created_at'] = do_hook('time.local.format', $ticket['created_at'], $this->timeformat, $this->timezone);
        if (isset($staff['updated_at'])) $staff['updated_at'] = do_hook('time.local.format', $staff['updated_at'], $this->timeformat, $this->timezone);

        return $ticket;
    }

    /**
     * Update Single Client
     * @return ?array
     */
    public function update()
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
        return get_accepted_queries($this->request->inputs(), $query_to_column);
    }
}