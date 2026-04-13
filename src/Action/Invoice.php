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
use App\Model\ClientModel;
use App\Model\InvoiceModel;
use App\Model\CurrencyModel;
use App\Model\InvoiceItemModel;
use App\Model\InvocieStatusModel;
use App\Model\InvoiceItemTypeModel;
use LBM\Exception\ActionException;

class Invoice
{
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

    /** @var int $limit */
    protected int $limit;

    public function __construct()
    {
        $this->model = new InvoiceModel();
        $this->client_model = new ClientModel();
        $this->item_model = new InvoiceItemModel();
        $this->type_model = new InvoiceItemTypeModel();
        $this->currency_model = new CurrencyModel();
        $this->status_model = new InvocieStatusModel();
        $this->limit = do_hook('option.int', 'data.limit', 20);
    }

    /**
     * Get Latest
     * @param ?int $limit Latest Data Limit. Default is NULL For Application Data Limit
     * @return array
     */
    public function latest(?int $limit = null)
    {
        $columns = ['invoice_id', 'invoice_number', 'total', 'currency_id', 'currency_code', 'currency_symbol', 'cid', 'company_name', 'username', 'status_name', 'status_color', 'invoice_created_at'];

        return $this->model
                ->select($columns)
                ->join($this->currency_model->table, 'currency_relid', '=', $this->currency_model->id)
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->join($this->client_model->table, 'client_relid', '=', $this->client_model->id)
                ->order($this->model->id, 'DESC')
                ->limit($this->limit)
                ->get();

    }

    /**
     * Get Single Invoice From id/number
     * @param int $entity A Entity. Example: 1/inv-20241205
     * @param array $columns Columns to Get
     * @return array
     */
    public function single(int $entity, array $columns): array
    {
        // Throw Error If Empty Column(s) Given
        if (empty($columns)) {
            throw new ActionException("Invalid Column(s) In " . __METHOD__);
        }

        $where = [
            'invoice_id' => $entity,
            'invoice_number' => $entity
        ];

        return $this->model->select($columns)->where($where, '=', 'OR')->first();
    }

    /**
     * Count Per Status
     * @return array
     */
    public function countPerStatus()
    {
        $columns = ['status_name as label', 'count(invoice_id) as count', 'status_color as color'];
        return $this->model
                ->select($columns)
                ->groupBy('status_name', 'status_color')
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->get();
    }
}