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
use Laika\App\Model\ClientModel;
use LBM\Exception\ActionException;
use Laika\App\Model\ClientNoteModel;
use Laika\App\Model\ClientTokenModel;
use Laika\App\Model\StaffStatusModel;
use Laika\App\Model\ClientStatusModel;
use Laika\App\Model\ClientContactModel;
use Laika\App\Model\ClientServiceModel;
use Laika\App\Model\ClientServiceNoteModel;
use Laika\App\Model\ClientServiceAddonModel;
use Laika\App\Model\ClientServiceStatusModel;
use Laika\App\Model\ClientServiceConfigValueModel;

class ClientContact
{
    /** @var Request $request */
    protected Request $request;

    /** @var Response $redirect */
    protected Response $response;

    /** @var ClientModel $model */
    protected ClientModel $model;

    /** @var ClientStatusModel $status_model */
    protected ClientStatusModel $status_model;

    /** @var ClientNoteModel $note_model */
    protected ClientNoteModel $note_model;

    /** @var string $timezone */
    protected string $timezone;

    /** @var string $timeformat */
    protected string $timeformat;

    public function __construct(?Request $request = null, ?Response $response = null)
    {
        $this->request = empty($request) ? new Request() : $request;
        $this->response = empty($response) ? new Response() : $response;
        $this->model = new ClientModel();
        $this->status_model = new ClientStatusModel();
        $this->note_model = new ClientNoteModel();
        $this->timezone = do_hook('option', 'time.zone', 'UTC');
        $this->timeformat = do_hook('option', 'datetime.format', 'Y-M-d H:i:s');
    }

    /**
     * Get Clients By Page Number
     * @return array
     */
    public function limit(): array
    {
        $select = ['id', 'company_name', 'first_name', 'middle_name', 'last_name', 'username', 'email', 'phone_cc', 'phone_number', 'status_name', 'status_color', 'created_at'];
        $clients = $this->model
                ->select(implode(', ', $select))
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->where($this->queries(), '=', 'OR')
                ->offset($this->request->input('page', 1))
                ->limit(do_hook('option.int', 'data.limit', 20))
                ->get();

        // Set DateTime Format
        foreach ($clients as $k => $client) {
            $clients[$k]['created_at'] = do_hook('time.local.format', $client['created_at'], $this->timeformat, $this->timezone);
        }
        return $clients;
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

        $this->model = $this->model->select(implode(', ', $columns));
        // Join Statuses if Exists
        if (in_array('status_relid', $columns) || in_array('status_id', $columns) || in_array('status_name', $columns)
        ) {
            $this->model = $this->model->join($this->status_model->table, 'status_relid', '=', $this->status_model->id);
        }

        $staff = $this->model->where($where, '=', 'OR')
                    ->first();
        // Convert Timestamps to Local
        if (isset($staff['created_at'])) $staff['created_at'] = do_hook('time.local.format', $staff['created_at']);
        if (isset($staff['last_login_at'])) $staff['last_login_at'] = do_hook('time.local.format', $staff['last_login_at']);
        if (isset($staff['updated_at'])) $staff['updated_at'] = do_hook('time.local.format', $staff['updated_at']);

        return $staff;
    }

    /**
     * Update Single Client
     * @return ?array
     */
    public function update_client()
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