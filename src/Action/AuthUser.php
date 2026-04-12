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

use Laika\Core\Relay\Relays\Auth;
use LBM\Support\Initiate;
use Laika\Session\Relay\Session;
use Laika\Core\Relay\Relays\Request;
use Laika\Core\Relay\Relays\Header;
use Laika\Core\Relay\Relays\Visitor;
use App\Model\LoginLogModel;
use LANG;

class AuthUser
{
    /** @var LoginLogModel $model */
    protected LoginLogModel $model;

    /** @var string $timezone */
    protected string $timezone;

    /** @var string $timeformat */
    protected string $timeformat;

    public function __construct()
    {
        // Initiate Requirements
        $init = new Initiate();
        $init->common(ADMIN);

        // Assign Additionals
        $this->model = new LoginLogModel();
        $this->timezone = do_hook('option', 'time.zone', 'UTC');
        $this->timeformat = do_hook('option', 'datetime.format', 'Y-M-d H:i:s');
    }

    /**
     * Create Login Log
     * @param array $logs Logs to Insert
     * @return array
     */
    public function createLog(array $logs): array
    {
        return $this->model->transaction(function(LoginLogModel $m) use ($logs) {
            try {
                $m->insert($logs);
            } catch (\Throwable $th) {
                $message = do_hook('redirect.message', LANG::$logCreateFailed, $th->getMessage());
                return ['status' => false, 'message' => $message];
            }
            return ['status' => true, 'message' => LANG::$logInSuccessful];
        });
    }

    /**
     * Staff Login
     * @return ?array
     */
    public function staffLogin(): ?array
    {
        // Initiate User Auth
        Auth::setType(ADMIN)->init();

        // Check Valid User Data Stored in Session & Database
        $id = (int) substr(Session::get("id", ADMIN), 12);

        // Database Stored User
        $auth_user = Auth::user();

        // Return if Already Authenticated
        if (isset($auth_user['sid']) && ($id > 0) && ($id === (int) $auth_user['sid'])) {
            return ['status' => true, 'message' => LANG::$authenticated];
        }

        // Process Login
        return Request::isPost() ? $this->processStaffLogin() : null;
    }

    /**
     * Panel Login
     * @return ?array
     */
    public function panel_login(): ?array
    {
        // Initiate User Auth
        Auth::setType(PANEL)->init();

        // Check Valid User Data Stored in Session & Database
        $id = (int) substr(Session::get("id", PANEL, ''), 12);

        // Database Stored User
        $auth_user = Auth::user();

        // Return if Already Authenticated
        if (isset($auth_user['id']) && ($id > 0) && ($id === (int) $auth_user['id'])) {
            return ['status' => true, 'message' => LANG::$authenticated];
        }

        // Process Login
        return Request::isPost() ? $this->processPanelLogin() : null; // processPanelLogin Not Completed Yet
    }

    /**
     * Process Staff Login
     * @return ?array
     */
    protected function processStaffLogin(): ?array
    {
        // Validate CSRF
        $csrf_response = do_hook('csrf.validate', ADMIN);
        if (!$csrf_response['status']) {
            return $csrf_response;
        }

        // Get Staff
        $input = Request::input('user');
        // Staff Columns To Get
        $columns = ['sid', 'password', 'role_name', 'permissions', 'first_name', 'last_name', 'username', 'email', 'status_name'];

        $staff = (new Staff())->single($input, $columns);

        // Check Staff Exists & Active
        if (empty($staff) || ($staff['status_name'] != 'active')) {
            return ['status' => false, 'message' => LANG::$invalidUser];
        }

        // Check Password is Valid
        $password = $staff['password'];
        // Unset Password
        unset($staff['password']);
        // Check Password is Valid
        if (!password_verify(Request::input('password'), $password)) {
            return ['status' => false, 'message' => LANG::$invalidUser];
        }

        try {
            // Set Auth User Data
            Auth::create($staff);
            // Log Login
            $logs = [
                'type' => 'staff',
                'relid' => $staff['sid'],
                'ip_address' => Visitor::ip(),
                'user_agent' => Visitor::userAgent(),
            ];
            $this->createLog($logs);
        } catch (\Throwable $th) {
            $message = do_hook('redirect.message', LANG::$generalError, $th->getMessage());
            return ['status' => false, 'message' => $message];
        }
        // Set Auth Session
        Session::for(ADMIN);
        Session::set("id", bin2hex(random_bytes(6)) . $staff['sid']);

        // Redirect to Dashboard
        return ['status' => true, 'message' => sprintf(LANG::$welcome, $staff['first_name'])];
    }

    /**
     * Validate Staff Login
     * @return ?array
     */
    public function validateStaffLogin(): ?array
    {
        // Initiate User Auth
        Auth::setType(ADMIN)->init();
        // Check Valid User Data Stored in Session & Database
        $random_id = Session::get("id");

        // Get Authenticated ID
        $len = strlen((string) $random_id);
        $id = $len === 0 ? 0 : (int) $random_id[$len -1];

        // Database Stored User
        $auth_user = $auth->user(ADMIN);

        // Redirect to Login Page if Not Authenticated
        if (empty($random_id) || !isset($auth_user['sid']) || ($id == 0) || ((int) $auth_user['sid'] !== $id)) {
            // Destroy Previous Data if Exists
            $auth->destroy();
            // Remove Staff Data if Exists
            Session::regenerate();
            return ['status' => false, 'message' => \LANG::$unauthenticated];
        }
        return null;
    }

    /**
     * Validate Staff Login
     * @return ?array
     */
    public function validatePanelLogin(): ?array
    {
        //
        return null;
    }

    /**
     * Process Panel Login
     * @return ?array
     */
    protected function processPanelLogin(): ?array
    {
        return null;
    }
}