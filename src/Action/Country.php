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
        $this->limit = option_int('data_limit', 20);
    }

    /**
     * Get Single Data
     * @param int|string $entity A Entity. Example: 1, 'US', 'USA'
     * @return array
     */
    public function single(int|string $entity): array
    {
        $where = [
            'country_id'    =>  $entity,
            'iso2'          =>  $entity,
            'iso3'          =>  $entity,
        ];
        return $this->model->where($where, '=', 'OR')->first();
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