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

use Laika\Core\Service\Request;
use Laika\Core\Service\Date;
use App\Model\OrderModel;
use App\Model\OrderStatusModel;
use App\Model\PromoCodeModel;
use App\Model\ClientModel;
use App\Model\CurrencyModel;
use App\Model\InvoiceModel;
use App\Model\InvoiceStatusModel;

class Order
{
    /** @var OrderModel $model */
    protected OrderModel $model;

    /** @var OrderStatusModel $status_model */
    protected OrderStatusModel $status_model;

    /** @var PromoCodeModel $promo_model */
    protected PromoCodeModel $promo_model;

    /** @var ClientModel $client_model */
    protected ClientModel $client_model;

    /** @var InvoiceModel $invoice_model */
    protected InvoiceModel $invoice_model;

    /** @var CurrencyModel $currency_model */
    protected CurrencyModel $currency_model;

    /** @var InvoiceStatusModel $inv_status_model */
    protected InvoiceStatusModel $inv_status_model;

    /** @var array $columns */
    protected array $columns;

    public function __construct()
    {
        $this->model = new OrderModel();
        $this->status_model = new OrderStatusModel();
        $this->client_model = new ClientModel();
        $this->promo_model = new PromoCodeModel();
        $this->currency_model = new CurrencyModel();
        $this->invoice_model = new InvoiceModel();
        $this->inv_status_model = new InvoiceStatusModel();

        $this->columns = [
            // Order Columns
            "{$this->model->table}.oid",
            "{$this->model->table}.order_number",
            "{$this->model->table}.amount",
            "{$this->model->table}.order_from_ip",
            "{$this->model->table}.fraud_score",
            "{$this->model->table}.order_created_at",
            "{$this->model->table}.order_updated_at",
            // Client Columns
            "{$this->client_model->table}.cid",
            "{$this->client_model->table}.company_name",
            "{$this->client_model->table}.first_name",
            "{$this->client_model->table}.middle_name",
            "{$this->client_model->table}.last_name",
            "{$this->client_model->table}.username",
            "{$this->client_model->table}.email",
            "{$this->client_model->table}.phone_cc",
            "{$this->client_model->table}.phone_number",
            // Status Columns
            "{$this->status_model->table}.status_name",
            "{$this->status_model->table}.status_color",
            // Currency Columns
            "{$this->currency_model->table}.currency_code",
            "{$this->currency_model->table}.prefix_symbol",
            "{$this->currency_model->table}.suffix_symbol",
            "{$this->currency_model->table}.exchange_rate",
            // Invoice Columns
            "{$this->invoice_model->table}.invoice_id",
            "{$this->invoice_model->table}.invoice_number",
            "{$this->invoice_model->table}.payment_gateway",
            // Promo Columns
            "{$this->promo_model->table}.promo_code",
            "{$this->promo_model->table}.promo_type",
            "{$this->promo_model->table}.promo_value",
        ];
    }

    ##############################################################################################
    /*====================================== EXTERNAL API ======================================*/
    ##############################################################################################
    /**
     * Get Orders By Page Number
     * @return array
     */
    public function limit(): array
    {
        if (Request::input('search')) {
            $input = Request::input('search');
            $where = [
                "{$this->client_model->table}.company_name" => "{$input}%",
                "{$this->client_model->table}.first_name" => "{$input}%",
                "{$this->client_model->table}.last_name" => "{$input}%",
                "{$this->client_model->table}.username" => "{$input}%",
                "{$this->client_model->table}.email" => "{$input}%",
                "{$this->client_model->table}.phone_number" => "{$input}%",
                "{$this->model->table}.status_name" => "{$input}%"
            ];
            return $this->model
                ->select($this->columns)
                ->join($this->status_model->table, "{$this->model->table}.status_relid", "=", "{$this->status_model->table}.{$this->status_model->id}")
                ->join($this->client_model->table, "{$this->model->table}.client_relid", "=", $this->client_model->id)
                ->join($this->promo_model->table, "{$this->model->table}.promo_relid", "=", $this->promo_model->id)
                ->join($this->currency_model->table, "{$this->model->table}.currency_relid", "=", $this->currency_model->id)
                ->join($this->invoice_model->table, "{$this->model->table}.invoice_relid", "=", $this->invoice_model->id)
                ->join($this->inv_status_model->table, "{$this->invoice_model->table}.status_relid", "=", "{$this->inv_status_model->table}.{$this->inv_status_model->id}")
                ->where($where, 'LIKE', 'OR')
                ->page(page_number())
                ->order($this->model->id)
                ->limit(data_limit())
                ->get();
        }
        return $this->model
                ->select($this->columns)
                ->join($this->status_model->table, "{$this->model->table}.status_relid", "=", "{$this->status_model->table}.{$this->status_model->id}")
                ->join($this->client_model->table, "{$this->model->table}.client_relid", "=", $this->client_model->id)
                ->join($this->promo_model->table, "{$this->model->table}.promo_relid", "=", $this->promo_model->id)
                ->join($this->currency_model->table, "{$this->model->table}.currency_relid", "=", $this->currency_model->id)
                ->join($this->invoice_model->table, "{$this->model->table}.invoice_relid", "=", $this->invoice_model->id)
                ->join($this->inv_status_model->table, "{$this->invoice_model->table}.status_relid", "=", "{$this->inv_status_model->table}.{$this->inv_status_model->id}")
                ->where($this->queries(), '=', 'OR')
                ->page(page_number())
                ->order($this->model->id)
                ->limit(data_limit())
                ->get();
    }

    /**
     * Get Single Client From id/Email/Username
     * @param int|string $entity
     * @return array
     */
    public function single(int|string $entity): array
    {
        $where = [
            'oid'           =>  $entity,
            'order_number'  =>  $entity
        ];

        return $this->model
                    ->select($this->columns)
                    ->join($this->status_model->table, "{$this->model->table}.status_relid", "=", "{$this->status_model->table}.{$this->status_model->id}")
                    ->join($this->client_model->table, "{$this->model->table}.client_relid", "=", $this->client_model->id)
                    ->join($this->promo_model->table, "{$this->model->table}.promo_relid", "=", $this->promo_model->id)
                    ->join($this->currency_model->table, "{$this->model->table}.currency_relid", "=", $this->currency_model->id)
                    ->join($this->invoice_model->table, "{$this->model->table}.invoice_relid", "=", $this->invoice_model->id)
                    ->join($this->inv_status_model->table, "{$this->invoice_model->table}.status_relid", "=", "{$this->inv_status_model->table}.{$this->inv_status_model->id}")
                    ->where($where, '=', 'OR')
                    ->first();
    }

    /**
     * Client Orders
     * @param int $client_relid
     * @return array
     */
    public function clientOrders(int $client_relid): array
    {
        return $this->model
                ->select($this->columns)
                ->join($this->status_model->table, "{$this->model->table}.status_relid", "=", "{$this->status_model->table}.{$this->status_model->id}")
                ->join($this->client_model->table, "{$this->model->table}.client_relid", "=", $this->client_model->id)
                ->join($this->promo_model->table, "{$this->model->table}.promo_relid", "=", $this->promo_model->id)
                ->join($this->currency_model->table, "{$this->model->table}.currency_relid", "=", $this->currency_model->id)
                ->join($this->invoice_model->table, "{$this->model->table}.invoice_relid", "=", $this->invoice_model->id)
                ->join($this->inv_status_model->table, "{$this->invoice_model->table}.status_relid", "=", "{$this->inv_status_model->table}.{$this->inv_status_model->id}")
                ->where(["{$this->model->table}.client_relid" => $client_relid], '=', 'OR')
                ->order($this->model->id, 'DESC')
                ->get();
    }

    /**
     * Count Staffs
     * @return int
     */
    public function count(): int
    {
        return $this->model->select($this->model->id)->count();
    }

    /**
     * Count Staffs By Query
     * @return int
     */
    public function countByQuery(): int
    {
        if (Request::input('search')) {
            $input = Request::input('search');
            $where = [
                "{$this->client_model->table}.company_name" => "{$input}%",
                "{$this->client_model->table}.first_name" => "{$input}%",
                "{$this->client_model->table}.last_name" => "{$input}%",
                "{$this->client_model->table}.username" => "{$input}%",
                "{$this->client_model->table}.email" => "{$input}%",
                "{$this->client_model->table}.phone_number" => "{$input}%",
                "{$this->model->table}.status_name" => "{$input}%"
            ];
            return $this->model
                ->select($this->model->id)
                ->where($where, 'LIKE', 'OR')
                ->count();
        }
        return $this->model
                ->select($this->model->id)
                ->where($this->queries(), '=', 'OR')
                ->count();
    }

    // /**
    //  * Count Active Staffs
    //  * @param string $status
    //  * @return int
    //  */
    // public function countByStatus(string $status): int
    // {
    //     return $this->model
    //                 ->select($this->model->id)
    //                 ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
    //                 ->where(['status_name' => strtolower($status)])
    //                 ->count();
    // }

    // /**
    //  * Count Created At Current Month Data
    //  * @return int
    //  */
    // public function countCurrentMonth(): int
    // {
    //     $first_day = Date::modify('first day of this month')->format('Y-m-d H:i:s');
    //     return $this->model
    //                 ->select($this->model->id)
    //                 ->where(['client_created_at' => $first_day], '>')
    //                 ->count();
    // }

    /**
     * Statuses List
     * @return array
     */
    public function statusList(): array
    {
        $list = $this->status_model->get();
        return array_column($list, 'status_color', 'status_name');
    }

    ##############################################################################################
    /*====================================== INTERNAL API ======================================*/
    ##############################################################################################
    /**
     * Get Accepted Queries
     * @return array
     */
    protected function queries(): array
    {
        $query_to_column = [
            "id" => "{$this->model->table}.oid",
            "company" => "{$this->client_model->table}.company_name",
            "phone" => "{$this->client_model->table}.phone_number",
            "username" => "{$this->client_model->table}.username",
            "email" => "{$this->client_model->table}.email",
            "fname" => "{$this->client_model->table}.first_name",
            "lname" => "{$this->client_model->table}.last_name",
            "status" => "{$this->model->table}.status_name",
            "promo" => "{$this->promo_model->table}.promo_code"
        ];
        return query_to_columns($query_to_column);
    }
}