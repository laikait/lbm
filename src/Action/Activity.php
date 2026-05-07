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

use App\Model\ActivityLogModel;
use Laika\Core\Relay\Relays\Visitor;
use LANG;

class Activity
{
    /** @var ActivityLogModel $model */
    protected ActivityLogModel $model;

    /** @var int $limit */
    protected int $limit;

    public function __construct()
    {
        $this->model = new ActivityLogModel();
        $this->limit = do_hook('option.int', 'data.limit', 20);
    }

    /**
     * Get Single Activity From id
     * @param int $id A Entity. Example: 1
     * @return array
     */
    public function single(int $id): array
    {
        return $this->model->select()->where(['id' => $id])->first();
    }

    /**
     * Get Latest Activities
     * @param ?int $limit Latest Data Limit. Default is NULL For Application Data Limit
     * @return array
     */
    public function latest(?int $limit = null): array
    {
        return $this->model->order($this->model->id, 'DESC')->limit($limit ?: $this->limit)->get();
    }

    /**
     * Get Activities By Type
     * @param string $type Activity Creator Type. Accepted Values: 'client', 'staff', 'system'
     * @return array
     */
    public function byType(string $type): array
    {
        return $this->model->where(['creator_type' => $type])->order($this->model->id, 'DESC')->get();
    }

    /**
     * Get Activities By Type
     * @param string $type Activity Type. Accepted Values: 'client', 'staff', 'system'
     * @param ?int $id Creator ID. Example: Client ID, Staff ID, Default is Null for System Activities
     * @return array
     */
    public function byTypeAndId(string $type, ?int $id = null): array
    {
        return $this->model->where(['creator_type' => $type, 'creator_id' => $id])->order($this->model->id, 'DESC')->get();
    }

    /**
     * Insert Activity
      * @param array{type: string,id?: int,short: string,long: string,changes?: array<string, array{old: mixed, new:mixed}>} $data
      * @return array{message: string, status: bool}
      * @throws \InvalidArgumentException
     */
    public function addActivity(array $data): array
    {
        if (empty($data['type']) || empty($data['short']) || empty($data['long'])) {
            throw new \InvalidArgumentException('Activity Type, Short Description and Long Description are required! Keys: type, short, long');
        }

        if (!in_array(strtolower($data['type']), ['client', 'staff', 'system'])) {
            throw new \InvalidArgumentException('Invalid Activity Type! Accepted Values: client, staff, system');
        }

        // Prepare Data
        $type = strtolower($data['type']);
        if ($type !== 'system' && empty($data['id'])) {
            throw new \InvalidArgumentException('Creator ID is required for Client and Staff Activities! Key: id');
        }

        try {
            // Get Old Data for Change Log
            $args = [
                'creator_type'  =>  $type,
                'creator_id'    =>  $data['id'] ?? null,
                'action_short'  =>  $data['short'] ?? '',
                'action_long'   =>  $data['long'] ?? '',
                'changes'       =>  serialize($data['changes'] ?? []),
                'log_from_ip'   =>  Visitor::ip()
            ];
            // Insert Log
            $this->model->insert($args);
            return ['message' => LANG::$logCreateSuccessful, 'status' => true];
        } catch (\Exception $e) {}
        return ['message' => LANG::$logCreateFailed, 'status' => false];
    }
}