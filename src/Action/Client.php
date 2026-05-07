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
use Laika\Core\Relay\Relays\Vault;
use Laika\Core\Relay\Relays\Regex;
use LANG;

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

    /** @var CurrencyModel $currency_model */
    protected CurrencyModel $currency_model;

    /** @var int $limit */
    protected int $limit;

    /** @var array $columns */
    protected array $columns;

    public function __construct()
    {
        $this->model = new ClientModel();
        $this->status_model = new ClientStatusModel();
        $this->note_model = new ClientNoteModel();
        $this->country_model = new CountryModel();
        $this->currency_model = new CurrencyModel();
        $this->limit = do_hook('option.int', 'data.limit', 20);
        $this->columns = [
            // Client Columns
            "{$this->model->table}.cid",
            "{$this->model->table}.company_name",
            "{$this->model->table}.first_name",
            "{$this->model->table}.middle_name",
            "{$this->model->table}.last_name",
            "{$this->model->table}.username",
            "{$this->model->table}.email",
            "{$this->model->table}.phone_cc",
            "{$this->model->table}.phone_number",
            "{$this->model->table}.street",
            "{$this->model->table}.city",
            "{$this->model->table}.state",
            "{$this->model->table}.postcode",
            "{$this->model->table}.client_created_at",
            "{$this->model->table}.client_updated_at",
            // Country Columns
            "{$this->country_model->table}.iso2",
            "{$this->country_model->table}.iso3",
            "{$this->country_model->table}.country_name",
            // Status Columns
            "{$this->status_model->table}.status_name",
            "{$this->status_model->table}.status_color",
            // Currency Columns
            "{$this->currency_model->table}.currency_id",
            "{$this->currency_model->table}.currency_code",
            "{$this->currency_model->table}.currency_symbol"
        ];
    }

    ##############################################################################################
    /*====================================== EXTERNAL API ======================================*/
    ##############################################################################################
    /**
     * Get Clients By Page Number
     * @return array
     */
    public function limit(): array
    {
        if (Request::input('search')) {
            $input = Request::input('search');
            $where = [
                "{$this->model->table}.company_name" => "{$input}%",
                "{$this->model->table}.first_name" => "{$input}%",
                "{$this->model->table}.middle_name" => "{$input}%",
                "{$this->model->table}.last_name" => "{$input}%",
                "{$this->model->table}.username" => "{$input}%",
                "{$this->model->table}.email" => "{$input}%",
                "{$this->model->table}.phone_number" => "{$input}%",
                "{$this->model->table}.status_name" => "{$input}%"
            ];
            return $this->model
                ->select($this->columns)
                ->join($this->status_model->table, "{$this->model->table}.status_relid", '=', "{$this->status_model->table}.{$this->status_model->id}")
                ->join($this->country_model->table, "{$this->model->table}.country_relid", '=', "{$this->country_model->table}.{$this->country_model->id}")
                ->join($this->currency_model->table, "{$this->model->table}.currency_relid", '=', "{$this->currency_model->table}.{$this->currency_model->id}")
                ->where($where, 'LIKE', 'OR')
                ->offset(Request::input('page', 1))
                ->order($this->model->id)
                ->limit($this->limit)
                ->get();
        }
        return $this->model
                ->select($this->columns)
                ->join($this->status_model->table, "{$this->model->table}.status_relid", '=', "{$this->status_model->table}.{$this->status_model->id}")
                ->join($this->country_model->table, "{$this->model->table}.country_relid", '=', "{$this->country_model->table}.{$this->country_model->id}")
                ->join($this->currency_model->table, "{$this->model->table}.currency_relid", '=', "{$this->currency_model->table}.{$this->currency_model->id}")
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
        $where = [
            "{$this->model->table}.cid"       =>  $entity,
            "{$this->model->table}.username"  =>  $entity,
            "{$this->model->table}.email"     =>  $entity,
        ];

        return $this->model
                    ->select($this->columns)
                    ->join($this->status_model->table, "{$this->model->table}.status_relid", '=', "{$this->status_model->table}.{$this->status_model->id}")
                    ->join($this->country_model->table, "{$this->model->table}.country_relid", '=', "{$this->country_model->table}.{$this->country_model->id}")
                    ->join($this->currency_model->table, "{$this->model->table}.currency_relid", '=', "{$this->currency_model->table}.{$this->currency_model->id}")
                    ->where($where, '=', 'OR')
                    ->first();
    }

    /**
     * Count Staffs
     * @return int
     */
    public function count(): int
    {
        return $this->model
                    ->select($this->model->id)
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
        return $this->status_model->get();
    }

    /**
     * Client Statuses List With Color
     * @return array
     */
    public function statusAndColor(): array
    {
        $list = $this->status_model->get();
        return array_column($list, 'status_color', 'status_name');
    }

    /**
     * Add Client
     * @return ?array
     */
    public function addClient(): ?array
    {
        if (!Request::isPost()) {
            return null;
        }

        // show(Request::inputs(), true);

        // Validate Form
        $rules = [
            'fname'     =>  'required',
            'lname'     =>  'required',
            'username'  =>  'required|min:' . do_hook('option.int', 'username.min', 6),
            'email'     =>  'required|email',
            'password'  =>  'required|min:' . do_hook('option.int', 'password.min', 6),
            'cpassword' =>  'required|match:password',
            'status'    =>  'required|in:'.implode(',',array_column($this->statusList(), 'status_id'))
        ];
        $messages = [
            'fname.required'        =>  LANG::$requiredField,
            'lname.required'        =>  LANG::$requiredField,
            'username.required'     =>  LANG::$requiredField,
            'email.required'        =>  LANG::$requiredField,
            'password.required'     =>  LANG::$requiredField,
            'cpassword.required'    =>  LANG::$requiredField,
            'status.required'       =>  LANG::$requiredField,
            'username.min'          =>  sprintf(LANG::$minLength, do_hook('option.int', 'username.min', 6)),
            'email.email'           =>  LANG::$invalidEmail,
            'password.min'          =>  sprintf(LANG::$minLength, do_hook('option.int', 'password.min', 6)),
            'cpassword.match'       =>  LANG::$confirmPasswordNotMatchd,
            'status.in'             =>  LANG::$invalidOption,
        ];

        // Validate Request
        Request::validate($rules, $messages);

        // Validate Username Doesn't Exists
        if ($this->model->select($this->model->id)->where(['username' => Request::input('username', '')])->first()) {
            Request::addError('username', LANG::$alreadyExists);
            return ['message' => LANG::$alreadyExists, 'status' => false];
        }

        // Validate Email Doesn't Exists
        if ($this->model->select($this->model->id)->where(['email' => Request::input('email', '')])->first()) {
            Request::addError('username', LANG::$alreadyExists);
            return ['message' => LANG::$alreadyExists, 'status' => false];
        }

        // Validate Phone Number
        if (Request::input('phone_number')) {
            if (preg_match('/[^0-9\(\)\s\-]+/', Request::input('phone_number'))) {
                Request::addError('phone_number', LANG::$invalidPhoneNumber);
                return ['message' => LANG::$generalError, 'status' => false];
            }
        }

        // Check Request Error
        if (!empty(Request::errors())) {
            return ['message' => LANG::$generalError, 'status' => false];
        }

        // Insert New Client
        try {
            $staff = current_staff();
            $data = [
                'company_name'  => (string) Request::input('cname'),
                'first_name'  => (string) Request::input('fname'),
                'middle_name'  => (string) Request::input('mname'),
                'last_name'  => (string) Request::input('lname'),
                'email'  => (string) Request::input('email'),
                'username'  => (string) Request::input('username'),
                'password' => (string) Vault::hashPassword(Request::input('password')),
                'phone_cc' => (string) Request::input('phone_code'),
                'phone_number' => (string) Request::input('phone_number'),
                'street' => (string) Request::input('street'),
                'city' => (string) Request::input('city'),
                'state' => (string) Request::input('state'),
                'postcode' => (string) Request::input('zip'),
                'country_relid' => (int) Request::input('country'),
                'currency_relid' => (int) Request::input('currency'),
                'status_relid' => (int) Request::input('status'),
            ];

            // // Insert Note
            // $this->model->insert($data);
            // // Insert Activity Log
            // $client_href = "<a href=\"" . named('staff.client', ['client' => $clientID], true) . "\">{$clientID}</a>";
            // $staff_href = "<a href=\"" . named('staff.staff', ['staff' => $staff['sid']], true) . "\">{$staff['sid']}</a>";
            // $data = [
            //     'type'      =>  'staff',
            //     'id'        =>  $staff['sid'],
            //     'short'     =>  LANG::$noteAdded,
            //     'long'      =>  sprintf('A Note Added to Client #%s by Staff %s', $client_href, $staff_href)
            // ];
            // $log = Activity::addActivity($data);

            // if (!$log['status']) {
            //     return ['message' => $log['message'], 'status' => $log['status']];
            // }
            return ['message' => LANG::$noteCreateSuccessful, 'status' => true];

        } catch (\Throwable $th) {
            if (config('env.debug')) {
                throw new ActionException($th->getMessage());
            }
            return ['message' => LANG::$generalError, 'status' => false];
        }
        return null;
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
            "id" => "{$this->model->table}.cid",
            "username" => "{$this->model->table}.username",
            "email" => "{$this->model->table}.email",
            "fname" => "{$this->model->table}.first_name",
            "lname" => "{$this->model->table}.last_name",
            "status" => "{$this->status_model->table}.status_name"
        ];
        return query_to_columns($query_to_column);
    }
}