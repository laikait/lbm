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

use Laika\Core\Relay\Relays\Request;
use Laika\Core\Relay\Relays\Header;
use App\Model\ActivityLogModel;

class Activity
{
    /** @var ActivityLogModel $model */
    protected ActivityLogModel $model;

    /** @var string $timezone */
    protected string $timezone;

    /** @var string $timeformat */
    protected string $timeformat;

    /** @var int $limit */
    protected int $limit;

    public function __construct()
    {
        $this->model = new ActivityLogModel();
        $this->timezone = do_hook('option', 'time.zone', 'UTC');
        $this->timeformat = do_hook('option', 'datetime.format', 'Y-M-d H:i:s');
        $this->limit = do_hook('option.int', 'data.limit', 20);
    }

    /**
     * Get Single Activity From id
     * @param int $id A Entity. Example: 1
     * @param array $columns Columns to Get
     * @return array
     */
    public function single(int $id, array $columns): array
    {
        return $this->model->select($columns)->where(['id' => 1])->first();
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
}