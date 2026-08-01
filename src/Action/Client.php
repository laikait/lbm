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

use Laika\Service\Date;
use Laika\Service\Vault;
use Laika\Service\Request;
use Laika\Service\Activity;
use LBM\Model\ClientModel;
use LBM\Model\CountryModel;
use LBM\Model\InvoiceModel;
use LBM\Model\CurrencyModel;
use LBM\Model\PasswordModel;
use LBM\Model\ClientNoteModel;
use LBM\Model\StaffStatusModel;
use LBM\Model\ClientStatusModel;
use LBM\Model\InvoiceStatusModel;
use LBM\Model\ClientContactModel;
use LBM\Model\ClientServiceModel;
use LBM\Exception\ActionException;
use LBM\Model\ClientServiceNoteModel;
use LBM\Model\ClientServiceAddonModel;
use LBM\Model\ClientServiceStatusModel;
use LBM\Model\ClientServiceConfigValueModel;
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

    /** @var array $columns */
    protected array $columns;

    public function __construct()
    {
        $this->model = new ClientModel();
        $this->status_model = new ClientStatusModel();
        $this->note_model = new ClientNoteModel();
        $this->country_model = new CountryModel();
        $this->currency_model = new CurrencyModel();
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
            "{$this->model->table}.country_relid",
            "{$this->model->table}.currency_relid",
            "{$this->model->table}.status_relid",
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
            "{$this->currency_model->table}.prefix_symbol",
            "{$this->currency_model->table}.suffix_symbol",
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
                ->page(page_number())
                ->order($this->model->id)
                ->limit(data_limit())
                ->get();
        }
        return $this->model
                ->select($this->columns)
                ->join($this->status_model->table, "{$this->model->table}.status_relid", '=', "{$this->status_model->table}.{$this->status_model->id}")
                ->join($this->country_model->table, "{$this->model->table}.country_relid", '=', "{$this->country_model->table}.{$this->country_model->id}")
                ->join($this->currency_model->table, "{$this->model->table}.currency_relid", '=', "{$this->currency_model->table}.{$this->currency_model->id}")
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
                "{$this->model->table}.company_name" => "{$input}%",
                "{$this->model->table}.first_name" => "{$input}%",
                "{$this->model->table}.middle_name" => "{$input}%",
                "{$this->model->table}.last_name" => "{$input}%",
                "{$this->model->table}.username" => "{$input}%",
                "{$this->model->table}.email" => "{$input}%",
                "{$this->model->table}.phone_number" => "{$input}%",
                "{$this->status_model->table}.status_name" => "{$input}%"
            ];
            return $this->model
                ->select($this->model->id)
                ->join($this->status_model->table, "{$this->model->table}.status_relid", '=', "{$this->status_model->table}.{$this->status_model->id}")
                ->where($where, 'LIKE', 'OR')
                ->count();
        }
        return $this->model
                ->select($this->model->id)
                ->join($this->status_model->table, "{$this->model->table}.status_relid", '=', "{$this->status_model->table}.{$this->status_model->id}")
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
                    ->join($this->status_model->table, "{$this->model->table}.status_relid", '=', "{$this->status_model->table}.{$this->status_model->id}")
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
     * Add Client
     * @return ?array
     */
    public function addClient(): ?array
    {
        if (!Request::isPost()) return null;

        $username = Request::input('username', '');
        $email = Request::input('email', '');
        $password = Request::input('password', '');
        $username_minimum_length = option_int('username_min_length', 6);
        $password_minimum_length = option_int('password_min_length', 6);
        $statuses = implode(',', array_column(client_statuses(), 'status_id'));
        $countries = implode(',', array_column(get_countries(), 'country_id'));
        $currencies = implode(',', array_column(get_currencies(), 'currency_id'));

        // Validate Form
        $rules = [
            "fname"         =>  "required",
            "lname"         =>  "required",
            "username"      =>  "required|callback:has_no_client,{$username}|min:{$username_minimum_length}",
            "email"         =>  "required|email|callback:has_no_client,{$email}",
            "password"      =>  "required|min:{$password_minimum_length}|callback:validate_password,{$password}",
            "cpassword"     =>  "required|match:password",
            "status"        =>  "required|in:{$statuses}",
            "currency"      =>  "required|in:{$currencies}"
        ];

        // Set Optional Rules If Exists
        if (Request::input('cname'))        $rules['cname'] = "regex:/^[\w\d\s\.\&]+$/i";
        if (Request::input('mname'))        $rules['mname'] = "alpha";
        if (Request::input('phone_code'))   $rules['phone_code'] = "regex:/^[\+\d\-]{1,4}$/";
        if (Request::input('phone_number')) $rules['phone_number'] = "regex:/^[\d\(\)\s\-]+$/";
        if (Request::input('street'))       $rules['street'] = "regex:/^[\w\s\/\,\.\#\&\-]+$/";
        if (Request::input('city'))         $rules['city'] = "regex:/^[a-z\s\-]+$/i";
        if (Request::input('state'))        $rules['state'] = "regex:/^[a-z\s\-\/]+$/i";
        if (Request::input('postcode'))     $rules['postcode'] = "regex:/^[\w\s\-]+$/";
        if (Request::input('country'))      $rules['country'] = "in:{$countries}";

        $messages = [
            'fname.required'        =>  LANG::$requiredField,
            'lname.required'        =>  LANG::$requiredField,
            'username.required'     =>  LANG::$requiredField,
            'username.min'          =>  sprintf(LANG::$minLength, $username_minimum_length),
            'username.callback'     =>  LANG::$alreadyExists,
            'email.required'        =>  LANG::$requiredField,
            'email.email'           =>  LANG::$invalidEmail,
            'email.callback'        =>  LANG::$alreadyExists,
            'password.required'     =>  LANG::$requiredField,
            'password.min'          =>  sprintf(LANG::$minLength, $password_minimum_length),
            'password.callback'     =>  LANG::$passwordPolicyMismatch,
            'cpassword.required'    =>  LANG::$requiredField,
            'cpassword.match'       =>  LANG::$confirmPasswordNotMatchd,
            'status.required'       =>  LANG::$requiredField,
            'status.in'             =>  LANG::$invalidOption,
            'currency.required'     =>  LANG::$requiredField,
            'currency.in'           =>  LANG::$invalidOption
        ];

        // Set Optional Messages If Exists
        if (Request::input('cname'))        $messages['cname.regex'] = LANG::$invalidInput;
        if (Request::input('mname'))        $messages['mname.alpha'] = LANG::$invalidInput;
        if (Request::input('phone_code'))   $messages['phone_code.regex'] = LANG::$invalidPhoneCode;
        if (Request::input('phone_number')) $messages['phone_number.regex'] = LANG::$invalidPhoneNumber;
        if (Request::input('street'))       $messages['street.regex'] = LANG::$invalidInput;
        if (Request::input('city'))         $messages['city.regex'] = LANG::$invalidInput;
        if (Request::input('state'))        $messages['state.regex'] = LANG::$invalidInput;
        if (Request::input('postcode'))     $messages['postcode'] = LANG::$invalidInput;
        if (Request::input('country'))      $messages['country.regex'] = LANG::$invalidOption;

        // Validate Request
        Request::validate($rules, $messages);

        // Check Request Error
        if (!empty(Request::errors())) return response(false, LANG::$invalidRequest);

        // Insert New Client
        try {
            $staff = current_staff();
            $data = [
                'cuid'              =>  $this->model->uid(),
                'company_name'      =>  (string) Request::input('cname', ''),
                'first_name'        =>  (string) Request::input('fname', ''),
                'middle_name'       =>  (string) Request::input('mname', ''),
                'last_name'         =>  (string) Request::input('lname', ''),
                'email'             =>  (string) Request::input('email'),
                'username'          =>  (string) Request::input('username'),
                'phone_cc'          =>  (string) Request::input('phone_code'),
                'phone_number'      =>  (string) Request::input('phone_number'),
                'street'            =>  (string) Request::input('street'),
                'city'              =>  (string) Request::input('city'),
                'state'             =>  (string) Request::input('state'),
                'postcode'          =>  (string) Request::input('postcode'),
                'country_relid'     =>  (int) Request::input('country'),
                'currency_relid'    =>  (int) Request::input('currency'),
                'status_relid'      =>  (int) Request::input('status')
            ];

            // Insert Client
            try {
                $id = $this->model->transaction(function (ClientModel $m) use ($data) {
                    $id = (int) $m->insert($data);
                    $pmodel = new PasswordModel();
                    $pmodel->where(['rel_id' => $id, 'rel_type' => 'client'])->update(['revoked_at' => date('Y-m-d H:i:s')]);
                    $pmodel->insert([
                        'rel_id'    =>  $id,
                        'rel_type'  =>  'client',
                        'hash'      =>  Vault::hash(Request::input('password'))
                    ]);
                    return $id;
                });
            } catch (\Throwable $th) {
                if (DEBUG) throw new ActionException($th->getMessage());
            }

            // Insert Activity Log
            $client_href = "<a href=\"" . named('staff.client', ['client' => $id]) . "\">{$data['username']}</a>";
            $staff_href = "<a href=\"" . named('staff.staff', ['staff' => $staff['sid']]) . "\">{$staff['sid']}</a>";
            $log = sprintf(LANG::$newClientAddedBy, $client_href, $staff_href);

            Activity::author('staff', $staff['sid'])->log($log)->event('create');

            return response(true, LANG::$newClientAdded);
        } catch (\Throwable $th) {
            if (DEBUG) throw new ActionException($th->getMessage(), 500, $th);
        }
        return response(false, LANG::$generalError);
    }

    /**
     * Modify Client
     * @param int $cid
     * @return ?array
     */
    public function modifyClient(int $cid): ?array
    {
        if (!Request::isPost()) return null;

        // Validate Form
        $rules = [
            'first_name'    =>  'required|regex:/^[\w\s\.]+$/i',
            'last_name'     =>  'required|regex:/^[\w\s\.]+$/i',
            'status'        =>  'required|in:' . implode(',', array_column(client_statuses(), 'status_id')),
            'currency'      =>  'required|in:' . implode(',', array_column(get_currencies(), 'currency_id'))
        ];
        // Set Optional Rules If Exists
        if (Request::input('company_name')) $rules['company_name']  = 'regex:/^[\w\d\s\.\&]+$/i';
        if (Request::input('middle_name'))  $rules['middle_name']   = 'alpha';
        if (Request::input('phone_cc'))     $rules['phone_cc']      = 'regex:/^[\+\d\-]{1,4}$/';
        if (Request::input('phone_number')) $rules['phone_number']  = 'regex:/^[\d\(\)\s\-]+$/';
        if (Request::input('street'))       $rules['street']        = 'regex:/^[\w\s\/\,\#\-]+$/';
        if (Request::input('city'))         $rules['city']          = 'regex:/^[a-z\s\-]+$/i';
        if (Request::input('state'))        $rules['state']         = 'regex:/^[a-z\s\-\/]+$/i';
        if (Request::input('postcode'))     $rules['postcode']      = 'regex:/^[\w\s\-]+$/';
        if (Request::input('country'))      $rules['country']       = 'in:'.implode(',', array_column(get_countries(), 'country_id'));

        $messages = [
            'first_name.required'   =>  LANG::$requiredField,
            'first_name.regex'      =>  LANG::$unsupportedCharacter,
            'last_name.required'    =>  LANG::$requiredField,
            'last_name.regex'       =>  LANG::$unsupportedCharacter,
            'status.required'       =>  LANG::$requiredField,
            'status.in'             =>  LANG::$invalidOption,
            'currency.required'     =>  LANG::$requiredField,
            'currency.in'           =>  LANG::$invalidOption
        ];
        // Set Optional Messages If Exists
        if (Request::input('company_name')) $messages['company_name.regex'] =   LANG::$invalidInput;
        if (Request::input('middle_name'))  $messages['middle_name.alpha']  =   LANG::$invalidInput;
        if (Request::input('phone_cc'))     $messages['phone_cc.regex']     =   LANG::$invalidPhoneCode;
        if (Request::input('phone_number')) $messages['phone_number.regex'] =   LANG::$invalidPhoneNumber;
        if (Request::input('street'))       $messages['street.regex']       =   LANG::$invalidInput;
        if (Request::input('city'))         $messages['city.regex']         =   LANG::$invalidInput;
        if (Request::input('state'))        $messages['state.regex']        =   LANG::$invalidInput;
        if (Request::input('postcode'))     $messages['postcode']           =   LANG::$invalidInput;
        if (Request::input('country'))      $messages['country.regex']      =   LANG::$invalidOption;

        // Validate Request
        Request::validate($rules, $messages);

        // Check Request Error
        if (!empty(Request::errors())) return response(false, LANG::$invalidRequest);

        // Insert New Client
        try {
            $data = [
                'company_name'      =>  (string) Request::input('company_name', ''),
                'first_name'        =>  (string) Request::input('first_name', ''),
                'middle_name'       =>  (string) Request::input('middle_name', ''),
                'last_name'         =>  (string) Request::input('last_name', ''),
                'phone_cc'          =>  (string) Request::input('phone_cc'),
                'phone_number'      =>  (string) Request::input('phone_number'),
                'street'            =>  (string) Request::input('street'),
                'city'              =>  (string) Request::input('city'),
                'state'             =>  (string) Request::input('state'),
                'postcode'          =>  (string) Request::input('postcode'),
                'country_relid'     =>  (int) Request::input('country'),
                'currency_relid'    =>  (int) Request::input('currency'),
                'status_relid'      =>  (int) Request::input('status')
            ];

            // $client = get_client($cid);
            $client = $this->single($cid);
            $changes = Activity::changelog($client);

            // Validate Has Changes
            if (empty($changes)) return response(false, LANG::$noChanges);

            // Update Client
            try {
                $this->model->transaction(function (ClientModel $m) use ($data, $cid) {
                    $m->where(['cid' => $cid])->update($data);
                });
            } catch (\Throwable $th) {
                if (DEBUG) throw new ActionException($th->getMessage());
            }

            // Activity Log
            $staff = current_staff();

            // Add Activity Log Event
            $client_href = "<a href=\"" . named('staff.client', ['client' => $cid]) . "\">{$cid}</a>";
            $staff_href = "<a href=\"" . named('staff.staff', ['staff' => $staff['id']]) . "\">{$staff['id']}</a>";
            $log = sprintf(LANG::$clientUpdatedByStaff, $client_href, $staff_href);

            Activity::author('staff', $staff['id'])->log($log)->event('modify', $changes);
            Activity::insert();

            return response(true, LANG::$saveSuccess);
        } catch (\Throwable $th) {
            if (DEBUG) throw new ActionException($th->getMessage());
        }
        return response(false, LANG::$generalErro);
    }

    /**
     * Reset Password By Staff
     * @param int $cid
     * @param ?int $contact_id
     * @return ?array
     */
    public function resetPasswordByStaff(int $cid, ?int $contact_id = null): ?array
    {
        // Check Request is Post
        if (!Request::isPost()) return null;

        $expire_ttl = option('token_lifetime', '1800');

        // Reset Password
        try {
            $data = [
                'token'                 =>  $this->generateClientToken(32),
                'type'                  =>  'password_reset',
                'client_relid'          =>  $cid,
                'client_contact_relid'  =>  $contact_id,
                'expires'               =>  Date::modify("+{$expire_ttl} seconds")->format('Y-m-d H:i:s')
            ];

            // Insert Password Reset Token
            $tx = $this->token_model->transaction(function (ClientTokenModel $m) use ($cid, $data, $contact_id) {
                $realtime = Date::now()->format('Y-m-d H:i:s');

                is_null($contact_id) ?
                    $m->where(['type' => 'password_reset', 'client_relid' => $cid])
                        ->isNull('client_contact_relid')
                        ->update(['expires' => $realtime]) :
                    $m->where(['type' => 'password_reset', 'client_relid' => $cid, 'client_contact_relid' => $contact_id])
                        ->isNull('client_contact_relid')
                        ->update(['expires' => $realtime]);

                $id = $m->insert($data);
            });
        } catch (\Throwable $th) {
            if (DEBUG) throw new ActionException($th->getMessage());
            return response(false, LANG::$resetPasswordFailed);
        }

        // Add Activity Log
        $staff = current_staff();

        $contact_href = $contact_id ? "<a href=\"" . named('staff.client.contact', ['client' => $cid, 'contact' => $contact_id]) . "\">{$contact_id}</a>" : "";
        $client_href = "<a href=\"" . named('staff.client', ['client' => $cid]) . "\">{$cid}</a>";
        $staff_href = "<a href=\"" . named('staff.staff', ['staff' => $staff['id']]) . "\">{$staff['id']}</a>";
        $log = $contact_id ?
                sprintf(LANG::$clientContactPasswordResetSuccess, $client_href, $contact_href, $staff_href) :
                sprintf(LANG::$clientPasswordResetSuccess, $client_href, $staff_href);
        
        // Insert Activity
        Activity::author('staff', $staff['id'])->log($log)->event('create');

        Activity::insert();
        return response(true, LANG::$passwordResetLinkSent);
    }

    /**
     * Reset Security Code By Staff
     * @param int $cid
     * @param ?int $contact_id
     * @return ?array
     */
    public function resetSecurityCodeByStaff(int $cid, ?int $contact_id = null): ?array
    {
        // Check Request is Post
        if (!Request::isPost()) return null;

        $expire_ttl = option('token_lifetime', '1800');

        // Reset Security Code
        try {
            $data = [
                'token'                 =>  bin2hex(random_bytes(2)),
                'type'                  =>  'support',
                'client_relid'          =>  $cid,
                'client_contact_relid'  =>  $contact_id
            ];

            // Insert Security Code
            $id = $this->token_model->transaction(function (ClientTokenModel $m) use ($cid, $data, $contact_id) {
                $realtime = Date::now()->format('Y-m-d H:i:s');
                is_null($contact_id) ?
                    $m->where(['type' => 'support', 'client_relid' => $cid])
                        ->isNull('client_contact_relid')
                        ->update(['expires' => $realtime]) :
                    $m->where(['type' => 'support', 'client_relid' => $cid, 'client_contact_relid' => $contact_id])
                        ->isNull('client_contact_relid')
                        ->update(['expires' => $realtime]);

                return $m->insert($data);
            });
        } catch (\Throwable $th) {
            if (DEBUG) throw new ActionException($th->getMessage());
            return response(false, LANG::$resetSecurityCodeFailed);
        }

        // Add Activity Log
        $staff = current_staff();

        $contact_href = $contact_id ? "<a href=\"" . named('staff.client.contact', ['client' => $cid, 'contact' => $contact_id]) . "\">{$contact_id}</a>" : "";
        $client_href = "<a href=\"" . named('staff.client', ['client' => $cid]) . "\">{$cid}</a>";
        $staff_href = "<a href=\"" . named('staff.staff', ['staff' => $staff['id']]) . "\">{$staff['id']}</a>";
        $log = $contact_id ?
                sprintf(LANG::$clientContactSecurityCodeResetSuccess, $client_href, $contact_href, $staff_href) :
                sprintf(LANG::$clientSecurityCodeResetSuccess, $client_href, $staff_href);
        
        // Insert Activity
        Activity::author('staff', $staff['id'])->log($log)->event('modify');

        Activity::insert();

        return response(true, LANG::$resetSecurityCodeSuccessful);
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

    /**
     * Generate Random Client Token
     * @param int $length
     * @return string
     */
    protected function generateClientToken(int $length): string
    {
        $token = bin2hex(random_bytes($length));
        $realtime = Date::now()->format('Y-m-d H:i:s');
        $exists = $this->token_model->where(['token' => $token])->where(['expires' => $realtime], '<=')->first();
        return $exists ? $this->generateClientToken($length) : $token;
    }
}