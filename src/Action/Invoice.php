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
use App\Model\ClientModel;
use App\Model\InvoiceModel;
use App\Model\CurrencyModel;
use App\Model\InvoiceItemModel;
use App\Model\InvoiceStatusModel;
use App\Model\InvoiceItemTypeModel;
use LBM\Exception\ActionException;

class Invoice
{
    /** @var InvoiceModel $model */
    protected InvoiceModel $model;

    /** @var CurrencyModel $currency_model */
    protected CurrencyModel $currency_model;

    /** @var ClientModel $client_model */
    protected ClientModel $client_model;

    /** @var InvoiceStatusModel $status_model */
    protected InvoiceStatusModel $status_model;

    /** @var int $limit */
    protected int $limit;

    public function __construct()
    {
        $this->model = new InvoiceModel();
        $this->currency_model = new CurrencyModel();
        $this->client_model = new ClientModel();
        $this->status_model = new InvoiceStatusModel();
        $this->limit = option_int('data.limit', 20);
    }

    /**
     * Get Limit
     * @param string|array|null $columns Default is null
     * @return array
     */
    public function limit(string|array|null $columns = null)
    {
        $columns = $columns ?: ['invoice_id', 'invoice_number', 'total', 'currency_id', 'currency_code', 'currency_symbol', 'cid', 'company_name', 'username', 'status_name', 'status_color', 'invoice_created_at'];

        return $this->model
                ->select($columns)
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->join($this->client_model->table, 'client_relid', '=', $this->client_model->id)
                ->order($this->model->id)
                ->limit($this->limit)
                ->get();

    }

    /**
     * Get Single Invoice From id/number
     * @param int $entity A Entity. Example: 1/inv-20241205
     * @return array
     */
    public function single(int $entity): array
    {
        $where = [
            'invoice_id' => $entity,
            'invoice_number' => $entity
        ];

        $columns = [
            // Invoice Columns
            'invoice_id',
            'invoice_number',
            'subtotal',
            'discount',
            'tax',
            'total',
            'credit_applied',
            'amount_paid',
            'invoice_due_date',
            'invoice_paid_date',
            'payment_method',
            'terms',
            'invoice_created_at',
            'invoice_updated_at',
            // Currency Columns
            'currency_id',
            'currency_code',
            'currency_symbol',
            // User Columns
            'cid',
            'company_name',
            'username',
            // Status Columns
            'status_id',
            'status_name',
            'status_color',
        ];

        return $this->model
                    ->select($columns)
                    ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                    ->join($this->client_model->table, 'client_relid', '=', $this->client_model->id)
                    ->join($this->currency_model->table, 'currency_relid', '=', $this->currency_model->id)
                    ->where($where, '=', 'OR')
                    ->first();
    }

    /**
     * Get Latest
     * @param ?int $limit Latest Data Limit. Default is NULL For Application Data Limit
     * @return array
     */
    public function latest(?int $limit = null): array
    {
        $columns = [
            'invoice_id',
            'invoice_number',
            'total',
            'invoices.currency_relid',
            'currency_id',
            'currency_code',
            'currency_symbol',
            'cid',
            'company_name',
            'username',
            'status_name',
            'status_color',
            'invoice_created_at'
        ];

        return $this->model
                ->select($columns)
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->join($this->client_model->table, 'client_relid', '=', $this->client_model->id)
                ->join($this->currency_model->table, 'invoices.currency_relid', '=', $this->currency_model->id)
                ->order($this->model->id, 'DESC')
                ->limit($limit ?: $this->limit)
                ->get();
    }

    /**
     * Group By Status
     * @return array
     */
    public function groupByStatus()
    {
        $columns = ['status_name as label', 'count(invoice_id) as total', 'status_color as color'];
        return $this->model
                ->select($columns)
                ->groupBy('status_name', 'status_color')
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->get();
    }

    /**
     * Total Spent By Client
     * @return string
     */
    public function totalSpentByClient(int $client_relid): string
    {
        $where = [
            'client_relid' => $client_relid,
            'status_name' => 'paid'
        ];

        $val = $this->model
                ->select(['sum(total) as total'])
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->where($where)
                ->first()['total'] ?? 0;
        return number_format((float) $val, 2, do_hook('option', 'decimal.symbol', '.'), ',');
    }

    public function totalOutstandingByClient(int $client_relid): string
    {
        $where = [
            'client_relid' => $client_relid,
            'status_name' => 'unpaid'
        ];

        $val = $this->model
                ->select(['sum(total - amount_paid) as total'])
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->where($where)
                ->first()['total'] ?? 0;
        return number_format((float) $val, 2, do_hook('option', 'decimal.symbol', '.'), ',');
    }

    /**
     * Client Invoices
     * @param int $client_relid Client Relid
     * @return array
     */
    public function clientInvoices(int $client_relid): array
    {
        $columns = [
            'invoice_id',
            'invoice_number',
            'total',
            'currency_id',
            'currency_code',
            'currency_symbol',
            'status_name',
            'status_color',
            'invoice_due_date',
            'invoice_paid_date',
            'payment_method',
            'invoice_created_at',
            'invoice_updated_at'
        ];

        return $this->model
                    ->select($columns)
                    ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                    ->join($this->currency_model->table, 'currency_relid', '=', $this->currency_model->id)
                    ->where(['client_relid' => $client_relid])
                    ->order('invoice_id', 'DESC')
                    ->get();
    }
}