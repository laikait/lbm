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

use Laika\Core\Http\Request;
use Laika\Core\Http\Response;
use Laika\App\Model\ClientModel;
use Laika\App\Model\InvoiceModel;
use Laika\App\Model\CurrencyModel;
use Laika\App\Model\InvoiceItemModel;
use Laika\App\Model\InvocieStatusModel;
use Laika\App\Model\InvoiceItemTypeModel;

class Invoice
{
    /** @var Request $request */
    protected Request $request;

    /** @var Response $redirect */
    protected Response $response;

    /** @var InvoiceModel $model */
    protected InvoiceModel $model;

    /** @var InvoiceItemModel $item_model */
    protected InvoiceItemModel $item_model;

    /** @var InvoiceItemTypeModel $type_model */
    protected InvoiceItemTypeModel $type_model;

    /** @var CurrencyModel $currency_model */
    protected CurrencyModel $currency_model;

    /** @var ClientModel $client_model */
    protected ClientModel $client_model;

    /** @var InvocieStatusModel $status_model */
    protected InvocieStatusModel $status_model;

    /** @var string $timezone */
    protected string $timezone;

    /** @var string $timeformat */
    protected string $timeformat;

    public function __construct(?Request $request = null, ?Response $response = null)
    {
        $this->request = empty($request) ? new Request() : $request;
        $this->response = empty($response) ? new Response() : $response;
        $this->model = new InvoiceModel();
        $this->client_model = new ClientModel();
        $this->item_model = new InvoiceItemModel();
        $this->type_model = new InvoiceItemTypeModel();
        $this->currency_model = new CurrencyModel();
        $this->status_model = new InvocieStatusModel();
        $this->timezone = do_hook('option', 'time.zone', 'UTC');
        $this->timeformat = do_hook('option', 'datetime.format', 'Y-M-d H:i:s');
    }

    /**
     * Get Single Invoice From id/number
     * @param int $id A Entity. Example: 1/inv-20241205
     * @param array $columns Columns to Get
     * @return array
     */
    public function single(int $id, array $columns): array
    {
        return $this->model->select($columns)->where(['id' => 1])->first();
    }

    /**
     * Get Latest
     * @param ?int $limit Latest Data Limit. Default is NULL For Application Data Limit
     * @return array
     */
    public function latest(?int $limit = null)
    {
        $columns = ['invoice_id', 'invoice_number', 'total', 'currency_id', 'currency_code', 'currency_symbol', 'cid', 'company_name', 'username', 'status_name', 'status_color', 'invoice_created_at'];
        $invoices = $this->model
                ->select($columns)
                ->join($this->currency_model->table, 'currency_relid', '=', $this->currency_model->id)
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->join($this->client_model->table, 'client_relid', '=', $this->client_model->id)
                ->order($this->model->id, 'DESC')
                ->limit($limit ?: do_hook('option.int', 'data.limit', 20))
                ->get();
        foreach($invoices as $k => $inv) {
            $invoices[$k]['invoice_created_at'] = do_hook('time.local.format', $inv['invoice_created_at'], $this->timeformat, $this->timezone);
        }
        return $invoices;
    }

    /**
     * Get Group By
     * @return array
     */
    public function group()
    {
        $columns = ['status_name as label', 'count(invoice_id) as count', 'status_color as color'];
        return $this->model
                ->select($columns)
                ->groupBy('status_name', 'status_color')
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->get();
    }
}