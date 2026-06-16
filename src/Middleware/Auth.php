<?php
/**
 * Name: Laika Shield
 * Provider: Laika IT
 * Email: strblackhawk@gmail.com
 */

declare(strict_types=1);

namespace LBM\Middleware;

use LBM\Service\Initiate;
use App\Model\StaffModel;
use App\Model\ClientModel;
use App\Model\LoginLogModel;
use App\Model\StaffRoleModel;
use App\Model\StaffStatusModel;
use App\Model\ClientStatusModel;
use Laika\Core\Interfaces\MiddlewareInterface;
use Laika\Core\Exceptions\MiddlewareException;
use Laika\Core\Service\{Url, Vault, Redirect, Request, Visitor, StaffAuth, ClientAuth};
use LANG;
// use Laika\Core\Service\{StaffAuth, ClientAuth, Url};

final class Auth implements MiddlewareInterface
{
    /** Constants */
    public CONST AUTHORIZED = 1;
    public CONST UNAUTHORIZED = 2;
    public CONST INVALID_TOKEN = 3;
    public CONST INVALID_USER_AGENT = 4;
    public CONST INVALID_DEVICE = 5;
    public CONST INVALID_OS = 6;

    /** @var bool Is Admin Slug */
    private bool $is_admin_slug = false;

    /** @var bool Is Client Slug */
    private bool $is_client_slug = false;

    /** @var string Redirect Dashboard Named */
    private string $dashboard;

    /** @var array Acceptable Guards Class List */
    private array $guards;

    /** @var string Current Slug */
    private string $slug;

    /** @var string Username */
    private string $username;

    /** @var string Password */
    private string $password;

    public function __construct()
    {
        // Initiate App
        Initiate::common();

        $this->slug = strtolower(Url::segment(1));
        $this->dashboard = 'home';
        $this->guards = [
            strtolower(ADMIN)   =>  [
                'auth'  =>  StaffAuth::class,
                'login' =>  'staff.login',
                'dash'  =>  'staff.dashboard',
            ],
            strtolower(PANEL)   =>  [
                'auth'  =>  ClientAuth::class,
                'login' =>  'client.login',
                'dash'  =>  'client.dashboard',
            ]
        ];

        $this->username = Request::input('username', '');
        $this->password = Request::input('password', '');
    }

    /**
     * Handle Authentication & Authorization
     * @return ?string
     */
    public function handle(callable $next, array $params): ?string
    {
        match (true) {
            match_url('staff.login')    =>  $this->processLogin(),
            match_url('client.login')   =>  $this->processLogin(),
            default                     =>  $this->validate()
        };
        return $next($params);
    }


    /**
     * Process Login
     * @return void
     */
    private function processLogin(): void
    {
        // Check Already Session Exists
        $this->checkSessionExists();

        // Check Request is Post
        if (!Request::isPost()) return;

        // Process Login
        $rules = [
            'username'  =>  'required',
            'password'  =>  'required',
        ];

        $message = [
            'username.required' =>  LANG::$requiredField,
            'password.required' =>  LANG::$requiredField
        ];

        // Validate Required Inputs
        Request::validate($rules, $message);

        // Return if Form Has Errors
        if (!empty(Request::errors())) return;

        $user = $this->getUser();

        // Validate User
        if (
            empty($user) || // Check Data is Not Empty
            ($user['status_name'] != 'active') || // Check User is Active
            !Vault::verifyPassword($this->password, $user['password']) // Match Password
        ) {
            alert_set(LANG::$invalidUser, false);
            return;
        }

        // Login
        unset($user['password']); // Unset password
        try {
            $this->guards[$this->slug]['auth']::login($user['id'], $user);
        } catch (\Throwable $th) {
            if (DEBUG) throw new MiddlewareException("Login Failed! {$th->getMessage()}");
            return;
        }
        // Log Data
        $lmodel = new LoginLogModel();
        try {
            $lmodel->transaction(function (loginlogModel $m) {
                $m->insert([
                    'type'          =>  $this->guards[$this->slug]['auth']::guard(),
                    'relid'         =>  $this->guards[$this->slug]['auth']::id(),
                    'ip_address'    =>  Visitor::ip(),
                    'user_agent'    =>  Visitor::userAgent()
                ]);
            });
        } catch (\Throwable $th) {
            if (DEBUG) throw new MiddlewareException("Login Log Insert Failed! {$th->getMessage()}");
        }
        // Make Login Log
        Redirect::with(LANG::$welcome, true)->to($this->guards[$this->slug]['dash']);
    }

    /**
     * Validate Authenticated
     * @return void
     */
    private function validate(): void
    {
        $data = $this->guards[$this->slug]['auth']::data();
        if (!$data['success'] || ($data['message'] !== AUTH::AUTHORIZED)) {
            $this->guards[$this->slug]['auth']::logout();
            Redirect::with(LANG::$unauthenticated, false)->to($this->guards[$this->slug]['login']);
        }
    }

    /**
     * Check Exists
     * @return void
     */
    private function checkSessionExists(): void
    {
        // Check Already Logged-in
        $data = $this->guards[$this->slug]['auth']::data();
        if (isset($data['success'], $data['message']) && $data['success'] && $data['message'] === Auth::AUTHORIZED) {
            Redirect::with(LANG::$alreadyExists, true)->to($this->guards[$this->slug]['dash']);
        }
        return;
    }

    /**
     * Get User From DB
     * @return array
     */
    private function getUser(): array
    {
        switch ($this->slug) {
            case strtolower(ADMIN):
                return $this->getStaff();
                break;
            
            case strtolower(PANEL):
                return $this->getClient();
                break;
            
            default:
                return [];
                break;
        }
    }

    /**
     * Get Staff
     * @return array
     */
    private function getStaff()
    {
        $smodel     =   new StaffModel();
        $srmodel    =   new StaffRoleModel();
        $ssmodel    =   new StaffStatusModel();
        $columns    =   [
            // Staff Columns
            "{$smodel->table}.sid as id",
            "{$smodel->table}.first_name",
            "{$smodel->table}.middle_name",
            "{$smodel->table}.last_name",
            "{$smodel->table}.username",
            "{$smodel->table}.email",
            "{$smodel->table}.password",
            // Role Columns
            "{$srmodel->table}.role_name",
            "{$srmodel->table}.permissions",
            // Status Columns
            "{$ssmodel->table}.status_name"
        ];
        $where = ["{$smodel->table}.username" => $this->username, "{$smodel->table}.email" => $this->username];
        return $smodel->select($columns)
            ->join($srmodel->table, "{$smodel->table}.role_relid", "=","{$srmodel->table}.{$srmodel->id}")
            ->join($ssmodel->table, "{$smodel->table}.status_relid", "=", "{$ssmodel->table}.{$ssmodel->id}")
            ->where($where, "=", 'OR')->first();
    }

    /**
     * Get Client
     * @return array
     */
    private function getClient()
    {
        $cmodel     =   new ClientModel();
        $csmodel    =   new ClientStatusModel();
        $columns    =   [
                // Client Columns
                "{$cmodel->table}.cid as id",
                "{$cmodel->table}.first_name",
                "{$cmodel->table}.middle_name",
                "{$cmodel->table}.last_name",
                "{$cmodel->table}.username",
                "{$cmodel->table}.email",
                "{$cmodel->table}.password",
                // Status Columns
                "{$csmodel->table}.status_name"
            ];
            $where = ["{$cmodel->table}.username" => $this->username, "{$cmodel->table}.email" => $this->username];
            return $cmodel->select($columns)
                ->join($csmodel->table, "{$cmodel->table}.status_relid", "=", "{$csmodel->table}.{$csmodel->id}")
                ->where($where, "=", 'OR')
                ->first();
    }
}