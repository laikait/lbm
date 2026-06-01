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

use App\Model\CurrencyModel;
use Laika\Core\Service\Math;

class Currency
{
    /** @var CurrencyModel $model */
    protected CurrencyModel $model;

    public function __construct()
    {
        $this->model = new CurrencyModel();
    }

    /**
     * Get All Currencies
     * @return array
     */
    public function list(): array
    {
        return $this->model->select()->order($this->model->id)->get();

    }

    /**
     * Get Single Currency From id or code
     * @param int|string $entity
     * @return array
     */
    public function single(int|string $entity): array
    {
        $where = [
            "{$this->model->table}.currency_id" => $entity,
            "{$this->model->table}.currency_code" => $entity
        ];

        return $this->model
                    ->select()
                    ->whereGroup(function($m) use ($where) {$m->where($where, '=', 'OR'); })
                    ->where(['is_active' => 'yes'])
                    ->first();
    }

    /**
     * Get Default Currency
     * @return array
     */
    public function default(): array
    {
        return $this->model->select()->where(['is_active' => 'yes', 'is_default' => 'yes'])->first();
    }

    /**
     * Get Exchange Rate
     * @param int|string $from
     * @param int|string $to
     * @return string
     */
    public function get_exchange_rate(int|string $from, int|string $to): string
    {
        $from_currency = $this->single($from);
        $to_currency = $this->single($to);

        if (!$from_currency || !$to_currency) return '0.0000';

        return Math::round(Math::div($to_currency['exchange_rate'], $from_currency['exchange_rate'], 4), 4);
    }

    /**
     * Convert Amount From One Currency To Another
     * @param int|float|string $amount
     * @param int|string $from
     * @param int|string $to
     * @return string
     */
    public function convert(int|float|string $amount, int|string $from, int|string $to): string
    {
        $exchange_rate = $this->get_exchange_rate($from, $to);
        return Math::round(Math::mul($amount, $exchange_rate, 4), 4);
    }
}