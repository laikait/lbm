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

use App\Model\ClientModel;
use App\Model\InvoiceModel;
use App\Model\CurrencyModel;
use App\Model\InvoiceItemModel;
use Laika\Core\Service\Request;
use App\Model\InvoiceStatusModel;
use App\Model\PaymentGatewayModel;
use LBM\Exception\ActionException;
use App\Model\InvoiceItemTypeModel;

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

    /** @var PaymentGatewayModel $payment_gateway_model */
    protected PaymentGatewayModel $payment_gateway_model;

    /** @var array $columns */
    protected array $columns;

    public function __construct()
    {
        $this->model = new InvoiceModel();
        $this->currency_model = new CurrencyModel();
        $this->client_model = new ClientModel();
        $this->status_model = new InvoiceStatusModel();
        $this->payment_gateway_model = new PaymentGatewayModel();
        $this->columns = [
            // Invocie Columns
            "{$this->model->table}.invoice_id",
            "{$this->model->table}.invoice_number",
            "{$this->model->table}.subtotal",
            "{$this->model->table}.discount",
            "{$this->model->table}.tax",
            "{$this->model->table}.total",
            "{$this->model->table}.credit_applied",
            "{$this->model->table}.amount_paid",
            "{$this->model->table}.invoice_due_date",
            "{$this->model->table}.invoice_paid_date",
            "{$this->model->table}.terms",
            "{$this->model->table}.invoice_created_at",
            "{$this->model->table}.invoice_updated_at",
            // Status Columns
            "{$this->status_model->table}.status_name",
            "{$this->status_model->table}.status_color",
            // Client Columns
            "{$this->client_model->table}.cid",
            "{$this->client_model->table}.first_name",
            "{$this->client_model->table}.middle_name",
            "{$this->client_model->table}.last_name",
            "{$this->client_model->table}.username",
            "{$this->client_model->table}.email",
            // Currency Columns
            "{$this->currency_model->table}.currency_id",
            "{$this->currency_model->table}.currency_code",
            "{$this->currency_model->table}.prefix_symbol",
            "{$this->currency_model->table}.suffix_symbol",
            "{$this->currency_model->table}.exchange_rate",
            // Payment Gateway Columns
            "{$this->payment_gateway_model->table}.gateway_id",
            "{$this->payment_gateway_model->table}.gateway_name",
            "{$this->payment_gateway_model->table}.gateway_slug",
            "{$this->payment_gateway_model->table}.display_name",
            "{$this->payment_gateway_model->table}.module_class",
            "{$this->payment_gateway_model->table}.logo_url",
            "{$this->payment_gateway_model->table}.is_active"
        ];
    }

    /**
     * Get Limit
     * @return array
     */
    public function limit(): array
    {
        return $this->model
                ->select($this->columns)
                ->join($this->status_model->table, "{$this->model->table}.status_relid", '=', "{$this->status_model->table}.{$this->status_model->id}")
                ->join($this->client_model->table, "{$this->model->table}.client_relid", '=', "{$this->client_model->table}.{$this->client_model->id}")
                ->join($this->currency_model->table, "{$this->model->table}.currency_relid", '=', "{$this->currency_model->table}.{$this->currency_model->id}")
                ->join($this->payment_gateway_model->table, "{$this->model->table}.payment_gateway", '=', "{$this->payment_gateway_model->table}.{$this->payment_gateway_model->id}")
                ->page(page_number())
                ->order($this->model->id)
                ->limit(data_limit())
                ->get();

    }

    /**
     * Get Single Invoice From id/number
     * @param int|string $entity A Entity. Example: 1/inv-20241205
     * @return array
     */
    public function single(int|string $entity): array
    {
        $where = [
            "{$this->model->table}.invoice_id" => $entity,
            "{$this->model->table}.invoice_number" => $entity
        ];

        return $this->model
                    ->select($this->columns)
                    ->join($this->status_model->table, "{$this->model->table}.status_relid", '=', "{$this->status_model->table}.{$this->status_model->id}")
                    ->join($this->client_model->table, "{$this->model->table}.client_relid", '=', "{$this->client_model->table}.{$this->client_model->id}")
                    ->join($this->currency_model->table, "{$this->model->table}.currency_relid", '=', "{$this->currency_model->table}.{$this->currency_model->id}")
                    ->join($this->payment_gateway_model->table, "{$this->model->table}.payment_gateway", '=', "{$this->payment_gateway_model->table}.{$this->payment_gateway_model->id}")
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
        return $this->model
                ->select($this->columns)
                ->join($this->status_model->table, "{$this->model->table}.status_relid", '=', "{$this->status_model->table}.{$this->status_model->id}")
                ->join($this->client_model->table, "{$this->model->table}.client_relid", '=', "{$this->client_model->table}.{$this->client_model->id}")
                ->join($this->currency_model->table, "{$this->model->table}.currency_relid", '=', "{$this->currency_model->table}.{$this->currency_model->id}")
                ->join($this->payment_gateway_model->table, "{$this->model->table}.payment_gateway", '=', "{$this->payment_gateway_model->table}.{$this->payment_gateway_model->id}")
                ->page(page_number())
                ->order($this->model->id, 'DESC')
                ->limit(data_limit($limit))
                ->get();
    }

    /**
     * Group By Status
     * @return array
     */
    public function groupByStatus(): array
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
     * @param int $client_relid
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
        return number_format((float) $val, 2, decimal_symbol(), thousand_separator());
    }

    /**
     * Total Outstanding By Client
     * @param int $client_relid
     * @return string
     */
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
        return number_format((float) $val, 2, decimal_symbol(), thousand_separator());
    }

    /**
     * Client Invoices
     * @param int $client_relid Client Relid
     * @return array
     */
    public function clientInvoices(int $client_relid): array
    {
        $columns = [
            // Invocie Columns
            "{$this->model->table}.invoice_id",
            "{$this->model->table}.invoice_number",
            "{$this->model->table}.subtotal",
            "{$this->model->table}.discount",
            "{$this->model->table}.tax",
            "{$this->model->table}.total",
            "{$this->model->table}.credit_applied",
            "{$this->model->table}.amount_paid",
            "{$this->model->table}.invoice_due_date",
            "{$this->model->table}.invoice_paid_date",
            "{$this->model->table}.terms",
            "{$this->model->table}.invoice_created_at",
            "{$this->model->table}.invoice_updated_at",
            // Currency Columns
            "{$this->currency_model->table}.currency_id",
            "{$this->currency_model->table}.currency_code",
            "{$this->currency_model->table}.prefix_symbol",
            "{$this->currency_model->table}.suffix_symbol",
            "{$this->currency_model->table}.exchange_rate",
            // Status Columns
            "{$this->status_model->table}.status_name",
            "{$this->status_model->table}.status_color"
        ];

        return $this->model
                    ->select($columns)
                    ->join($this->status_model->table, "{$this->model->table}.status_relid", '=', "{$this->status_model->table}.{$this->status_model->id}")
                    ->join($this->currency_model->table, "{$this->model->table}.currency_relid", '=', "{$this->currency_model->table}.{$this->currency_model->id}")
                    ->where(['client_relid' => $client_relid])
                    ->order('invoice_id', 'DESC')
                    ->get();
    }
}