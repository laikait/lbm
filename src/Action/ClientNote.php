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

use Laika\Service\CSRF;
use LBM\Model\StaffModel;
use LBM\Model\ClientModel;
use LBM\Model\ClientNoteModel;
use Laika\Service\Request;
use Laika\Service\Activity;
use LBM\Exception\ActionException;
use LANG;

class ClientNote
{
    /** @var ClientNoteModel $model */
    protected ClientNoteModel $model;

    /** @var ClientModel $cmodel */
    protected ClientModel $cmodel;

    /** @var StaffModel $smodel */
    protected StaffModel $smodel;

    /** @var array $columns */
    protected array $columns;

    public function __construct()
    {
        $this->model = new ClientNoteModel();
        $this->smodel = new StaffModel();
        $this->cmodel = new ClientModel();
        $this->columns = [
            // Note Columns
            "{$this->model->table}.note_id",
            "{$this->model->table}.note",
            "{$this->model->table}.note_created_at",
            // Client Columns
            "{$this->cmodel->table}.cid",
            "{$this->cmodel->table}.first_name as client_first_name",
            "{$this->cmodel->table}.last_name as client_last_name",
            "{$this->cmodel->table}.username as client_username",
            // Staff Columns
            "{$this->smodel->table}.sid",
            "{$this->smodel->table}.first_name as staff_first_name",
            "{$this->smodel->table}.last_name as staff_last_name",
            "{$this->smodel->table}.username as staff_username"

        ];
    }

    /**
     * Get Single Note By ID
     * @param int $id
     * @return array
     */
    public function single(int $id): array
    {
        return $this->model
                    ->select($this->columns)
                    ->join($this->cmodel->table, "{$this->cmodel->table}.cid", '=', "{$this->model->table}.client_relid")
                    ->join($this->smodel->table, "{$this->smodel->table}.sid", '=', "{$this->model->table}.staff_relid")
                    ->where(['note_id' => $id])
                    ->first();
    }

    /**
     * Get Notes By Clien ID
     * @param int $relid
     * @param string $orderBy
     * @return array
     */
    public function getByClientId(int $relid, string $orderBy = 'ASC'): array
    {
        return $this->model
                    ->select($this->columns)
                    ->join($this->cmodel->table, "{$this->cmodel->table}.cid", '=', "{$this->model->table}.client_relid")
                    ->join($this->smodel->table, "{$this->smodel->table}.sid", '=', "{$this->model->table}.staff_relid")
                    ->where(['client_relid' => $relid])
                    ->order($this->model->id, $orderBy)
                    ->get();
    }

    /**
     * Get Notes By Staff ID
     * @param int $relid
     * @param string $orderBy
     * @return array
     */
    public function getByStaffId(int $relid, string $orderBy = 'ASC'): array
    {
        return $this->model
                    ->select($this->columns)
                    ->join($this->cmodel->table, "{$this->cmodel->table}.cid", '=', "{$this->model->table}.client_relid")
                    ->join($this->smodel->table, "{$this->smodel->table}.sid", '=', "{$this->model->table}.staff_relid")
                    ->where(['staff_relid' => $relid])
                    ->order($this->model->id, $orderBy)
                    ->get();
    }

    /**
     * Get Latest
     * @param ?int $limit
     * @return array
     */
    public function latest(?int $limit = null): array
    {
        return $this->model
                    ->select($this->columns)
                    ->join($this->cmodel->table, "{$this->cmodel->table}.cid", '=', "{$this->model->table}.client_relid")
                    ->join($this->smodel->table, "{$this->smodel->table}.sid", '=', "{$this->model->table}.staff_relid")
                    ->order($this->model->id, 'DESC')
                    ->limit(data_limit($limit))
                    ->get();
    }

    /**
     * Add Note
     * @param int|string $clientID
     * @return ?array
     */
    public function addNote(int|string $clientID): ?array
    {
        if (!Request::isPost()) return null;

        // Validate Client ID
        if (!Request::input('cid') || (Request::input('cid') != $clientID)) {
            return response(false, LANG::$invalidRequest);
        }

        // Validate Form
        $rules = [
            'note' => 'required|max:1000'
        ];
        $messages = [
            'note.required' => LANG::$requiredField,
            'note.max' => sprintf(LANG::$maxCharLimit, 1000)
        ];

        Request::validate($rules, $messages);
        if (!empty(Request::errors())) response(false, LANG::$generalError);

        // Insert Note & Log
        try {
            $staff = current_staff();
            $data = [
                'client_relid' => $clientID,
                'staff_relid' => $staff['sid'],
                'note' => Request::input('note')
            ];

            // Insert Note
            $this->model->insert($data);
            // Insert Activity Log
            $client_href = "<a href=\"" . named('staff.client', ['client' => $clientID]) . "\">{$clientID}</a>";
            $staff_href = "<a href=\"" . named('staff.staff', ['staff' => $staff['sid']]) . "\">{$staff['username']}</a>";
            $log = sprintf('A Note Added to Client %s by Staff %s', $client_href, $staff_href);
            Activity::author('staff', $staff['sid'])->log($log)->event('create');

            return response(true, LANG::$noteCreateSuccessful);
        } catch (\Throwable $th) {
            if (DEBUG) throw new ActionException($th->getMessage(), 500, $th);
        }
        return response(false, LANG::$generalError);
    }
}