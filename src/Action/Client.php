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
use LBM\Exception\ActionException;
use App\Model\ClientNoteModel;
use App\Model\InvoiceModel;
use App\Model\InvoiceStatusModel;
use App\Model\CountryModel;
use App\Model\CurrencyModel;
use App\Model\ClientTokenModel;
use App\Model\StaffStatusModel;
use App\Model\ClientStatusModel;
use App\Model\ClientContactModel;
use App\Model\ClientServiceModel;
use App\Model\ClientServiceNoteModel;
use App\Model\ClientServiceAddonModel;
use App\Model\ClientServiceStatusModel;
use App\Model\ClientServiceConfigValueModel;
use Laika\Core\Relay\Relays\Date;

class Client
{
    /** @var ClientModel $model */
    protected ClientModel $model;

    /** @var ClientStatusModel $status_model */
    protected ClientStatusModel $status_model;

    /** @var ClientNoteModel $note_model */
    protected ClientNoteModel $note_model;

    /** @var CountryModel $country_model */
    protected CountryModel $country_model;

    /** @var int $limit */
    protected int $limit;

    public function __construct()
    {
        $this->model = new ClientModel();
        $this->status_model = new ClientStatusModel();
        $this->note_model = new ClientNoteModel();
        $this->limit = do_hook('option.int', 'data.limit', 20);
    }

    ##############################################################################################
    /*====================================== EXTERNAL API ======================================*/
    ##############################################################################################
    /**
     * Get Clients By Page Number
     * @param string|array|null $columns Default is null
     * @return array
     */
    public function limit(string|array|null $columns = null): array
    {
        $columns = $columns ?: ['cid', 'company_name', 'first_name', 'middle_name', 'last_name', 'username', 'email', 'phone_cc', 'phone_number', 'status_name', 'status_color', 'client_created_at', 'client_updated_at'];

        if (Request::input('search')) {
            $input = Request::input('search');
            $where = [
                "company_name" => "{$input}%",
                "first_name" => "{$input}%",
                "middle_name" => "{$input}%",
                "last_name" => "{$input}%",
                "username" => "{$input}%",
                "email" => "{$input}%",
                "phone_number" => "{$input}%",
                "status_name" => "{$input}%"
            ];
            return $this->model
                ->select($columns)
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->where($where, 'LIKE', 'OR')
                ->offset(Request::input('page', 1))
                ->order($this->model->id)
                ->limit($this->limit)
                ->get();
        }
        return $this->model
                ->select($columns)
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->where($this->queries(), '=', 'OR')
                ->offset(Request::input('page', 1))
                ->order($this->model->id)
                ->limit($this->limit)
                ->get();
    }

    /**
     * Get Single Client From id/Email/Username
     * @param int|string $entity
     * @return array
     */
    public function single(int|string $entity): array
    {
        // Throw Error If Empty Column(s) Given
        $columns = ['cid', 'company_name', 'first_name', 'middle_name', 'last_name', 'username', 'email', 'phone_cc', 'phone_number', 'address1', 'address2', 'city', 'state', 'postcode', 'iso2', 'iso3', 'country_name', 'status_name', 'status_color', 'client_created_at', 'client_updated_at', 'currency_id', 'currency_code', 'currency_symbol'];

        $where = [
            'cid'       =>  $entity,
            'username'  =>  $entity,
            'email'     =>  $entity,
        ];

        $country_model = new CountryModel();
        $currency_model = new CurrencyModel();
        return $this->model
                    ->select($columns)
                    ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                    ->join($country_model->table, 'country_relid', '=', $country_model->id)
                    ->join($currency_model->table, 'currency_relid', '=', $currency_model->id)
                    ->where($where, '=', 'OR')
                    ->first();
    }

    // /**
    //  * Update Single Client
    //  * @param int|string $entity Staff Entity. Example: id,username,email
    //  * @return ?array
    //  */
    // public function update(int|string $entity): ?array
    // {
    //     if (Request::isPost()) {
    //         return ['status' => true, 'message' => 'Success'];
    //     }
    //     return null;
    // }

    /**
     * Count Staffs
     * @return int
     */
    public function count(): int
    {
        return $this->model
                    ->select($this->model->id)
                    ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                    ->count();
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
                "company_name" => "{$input}%",
                "first_name" => "{$input}%",
                "middle_name" => "{$input}%",
                "last_name" => "{$input}%",
                "username" => "{$input}%",
                "email" => "{$input}%",
                "phone_number" => "{$input}%",
                "status_name" => "{$input}%"
            ];
            return $this->model
                ->select($this->model->id)
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->where($where, 'LIKE', 'OR')
                ->count();
        }
        return $this->model
                ->select($this->model->id)
                ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                ->where($this->queries(), '=', 'OR')
                ->count();
    }

    /**
     * Count Active Staffs
     * @param string $status
     * @return int
     */
    public function countByStatus(string $status): int
    {
        return $this->model
                    ->select($this->model->id)
                    ->join($this->status_model->table, 'status_relid', '=', $this->status_model->id)
                    ->where(['status_name' => strtolower($status)])
                    ->count();
    }

    /**
     * Count Created At Current Month Data
     * @return int
     */
    public function countCurrentMonth(): int
    {
        $first_day = Date::modify('first day of this month')->format('Y-m-d H:i:s');
        return $this->model
                    ->select($this->model->id)
                    ->where(['client_created_at' => $first_day], '>')
                    ->count();
    }

    /**
     * Client Statuses List
     * @return array
     */
    public function statusList(): array
    {
        $list = (new ClientStatusModel())->get();
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
        $query_to_column = ['id' => 'cid', 'username' => 'username', 'email' => 'email', 'fname' => 'first_name', 'lname' => 'last_name', 'status' => 'status_name'];
        return query_to_columns(Request::inputs(), $query_to_column);
    }
}