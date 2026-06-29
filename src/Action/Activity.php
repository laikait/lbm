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

use Laika\Model\Model;
use LBM\Model\StaffModel;
use LBM\Model\ClientModel;
use Laika\Service\Visitor;
use LBM\Exception\ActionException;
use InvalidArgumentException;
use LANG;

class Activity
{
    /** @var Model $model */
    protected Model $model;

    /** @var ClientModel $cmodel */
    protected ClientModel $cmodel;

    /** @var StaffModel $smodel */
    protected StaffModel $smodel;

    public function __construct()
    {
        $this->model = new Model();
        $this->cmodel = new ClientModel();
        $this->smodel = new StaffModel();
    }

    /**
     * Get Single Activity From id
     * @param int $id A Entity. Example: 1
     * @return array
     */
    public function single(int $id): array
    {
        return $this->model->table('activities')->select()->where(['log_id' => $id])->first();
    }

    /**
     * Get Latest Activities
     * @param ?int $limit Latest Data Limit. Default is NULL For Application Data Limit
     * @return array
     */
    public function latest(?int $limit = null): array
    {
        return $this->model->table('activities')->order('log_id', 'DESC')->limit(data_limit($limit))->get();
    }

    /**
     * Get Activities By Type
     * @param string $type Activity Creator Type. Accepted Values: 'client', 'staff', 'system'
     * @param ?int $limit Latest Data Limit. Default is NULL For Application Data Limit
     * @return array
     */
    public function byType(string $type, ?int $limit): array
    {
        $type = strtolower($type);
        return match ($type) {
            'client' => $this->model
                        ->table('activities')
                        ->select($this->clientColumns())
                        ->where(["{$this->model->table}.author_type" => 'client'])
                        ->join($this->cmodel->table, "{$this->model->table}.author_id", '=', "{$this->cmodel->table}.cid")
                        ->order("{$this->model->table}.log_id", 'DESC')
                        ->limit(data_limit($limit))
                        ->get(),
            'staff' => $this->model
                        ->table('activities')
                        ->select($this->staffColumns())
                        ->where(['author_type' => 'staff'])
                        ->join($this->smodel->table, "{$this->model->table}.author_id", '=', "{$this->smodel->table}.sid")
                        ->order("{$this->model->table}.log_id", 'DESC')
                        ->limit(data_limit($limit))
                        ->get(),
            'system' => $this->model->where(['author_type' => 'system'])->order($this->model->id, 'DESC')->get(),
            default => throw new InvalidArgumentException('Invalid Activity Type! Accepted Values: client, staff, system')
        };
    }

    /**
     * Get Activities By Type
     * @param string $type Activity Type. Accepted Values: 'client', 'staff', 'system'
     * @param ?int $id Creator ID. Example: Client ID, Staff ID, Default is Null for System Activities
     * @param ?int $limit Latest Data Limit. Default is NULL For Application Data Limit
     * @return array
     */
    public function byTypeAndId(string $type, ?int $id = null, ?int $limit = null): array
    {
        $type = strtolower($type);

        return match ($type) {
            'client' => $this->model
                        ->table('activities')
                        ->select($this->clientColumns())
                        ->where(["{$this->model->table}.author_type" => 'client', "{$this->model->table}.author_id" => $id])
                        ->join($this->cmodel->table, "{$this->model->table}.author_id", '=', "{$this->cmodel->table}.cid")
                        ->order("{$this->model->table}.log_id", 'DESC')
                        ->limit(data_limit($limit))
                        ->get(),
            'staff' => $this->model
                        ->table('activities')
                        ->select($this->staffColumns())
                        ->where(["{$this->model->table}.author_type" => 'staff', "{$this->model->table}.author_id" => $id])
                        ->join($this->smodel->table, "{$this->model->table}.author_id", '=', "{$this->smodel->table}.sid")
                        ->order("{$this->model->table}.log_id", 'DESC')
                        ->limit(data_limit($limit))
                        ->get(),
            'system' => $this->model
                        ->table('activities')
                        ->where(["{$this->model->table}.author_type" => 'system', "{$this->model->table}.author_id" => null])
                        ->order("{$this->model}.log_id", 'DESC')->get(),
            default => throw new InvalidArgumentException('Invalid Activity Type! Accepted Values: client, staff, system')
        };
    }

    ################################################################################################
    ####################################### INTERNAL METHODS #######################################
    ################################################################################################
    /**
     * Get Staff Columns for Activity Log
      * @return string[]
     */
    protected function staffColumns(): array
    {
        return [
            // Log Columns
            "{$this->model->table}.log_id",
            "{$this->model->table}.author_type",
            "{$this->model->table}.author_id",
            "{$this->model->table}.event",
            "{$this->model->table}.log",
            "{$this->model->table}.changes",
            "{$this->model->table}.from_ip",
            "{$this->model->table}.created_at as log_created_at",
            // Staff Columns
            "{$this->smodel->table}.username",
        ];
    }

    /**
     * Get Client Columns for Activity Log
      * @return string[]
     */
    protected function clientColumns(): array
    {
        return [
            // Log Columns
            "{$this->model->table}.log_id",
            "{$this->model->table}.author_type",
            "{$this->model->table}.author_id",
            "{$this->model->table}.event",
            "{$this->model->table}.log",
            "{$this->model->table}.changes",
            "{$this->model->table}.from_ip",
            "{$this->model->table}.created_at as log_created_at",
            // Staff Columns
            "{$this->cmodel->table}.username",
        ];
    }
}