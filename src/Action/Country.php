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

use App\Model\CountryModel;

class Country
{
    /** @var CountryModel $model */
    protected CountryModel $model;

    /** @var int $limit */
    protected int $limit;

    public function __construct()
    {
        $this->model = new CountryModel();
        $this->limit = do_hook('option.int', 'data.limit', 20);
    }

    /**
     * Get Single Data
     * @param int|string $entity A Entity. Example: 1
     * @param array $columns Columns to Get
     * @return array
     */
    public function single(int|string $entity, array $columns): array
    {
        $where = [
            'country_id'    =>  $entity,
            'iso2'          =>  $entity,
            'iso3'          =>  $entity,
        ];
        return $this->model->select($columns)->where($where, '=', 'OR')->first();
    }

    /**
     * Get list
     * @return array
     */
    public function list(): array
    {
        return $this->model->order($this->model->id)->get();
    }
}