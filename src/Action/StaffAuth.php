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
use Laika\Core\Relay\Relays\Visitor;
use Laika\Core\Relay\Relays\Csrf;
use App\Model\LoginLogModel;
use LANG;

class StaffAuth
{
    /** @var LoginLogModel $model */
    protected LoginLogModel $model;

    public function __construct()
    {
        // Initiate Requirements
        $init = new Initiate();
        $init->common(ADMIN);
        Session::for(ADMIN);
        Auth::setType(ADMIN)->init();

        // Assign Additionals
        $this->model = new LoginLogModel();
    }

    ######################################################################################
    /*================================== EXTERNAL API ==================================*/
    ######################################################################################
    /**
     * Staff Login
     * @return ?array
     */
    public function login(): ?array
    {
        // Check Valid User Data Stored in Session & Database
        $id = (int) substr(Session::get("id", ADMIN), 12);

        // Database Stored User
        $auth_user = Auth::user();

        // Return if Already Authenticated
        if (isset($auth_user['sid']) && ($id > 0) && ($id === (int) $auth_user['sid'])) {
            return ['status' => true, 'message' => LANG::$authenticated];
        }

        // Process Login
        return Request::isPost() ? $this->process() : null;
    }

    /**
     * Validate Staff Login
     * @return ?array
     */
    public function validate(): ?array
    {
        // Get Authenticated ID
        $id = (int) substr(Session::get('session_id'), 12);

        if ($id > 0) {
            return null;
        }

        // Database Stored User
        $auth_user = Auth::user();

        // Redirect to Login Page if Not Authenticated
        if (!isset($auth_user['sid']) || ((int) $auth_user['sid'] !== $id)) {
            // Destroy Previous Data if Exists
            Auth::destroy();
            // Remove Staff Data if Exists
            Session::regenerate();
            Session::end();
            return ['status' => false, 'message' => LANG::$unauthenticated];
        }
        return null;
    }

    ######################################################################################
    /*================================== INTERNAL API ==================================*/
    ######################################################################################
    /**
     * Process Staff Login
     * @return ?array
     */
    protected function process(): ?array
    {
        // Validate CSRF
        if (!Csrf::is_valid()) {
            return null;
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
        Session::set("id", bin2hex(random_bytes(6)) . $staff['sid']);

        // Redirect to Dashboard
        return ['status' => true, 'message' => sprintf(LANG::$welcome, $staff['first_name'])];
    }

    /**
     * Create Login Log
     * @param array $logs Logs to Insert
     * @return array
     */
    protected function createLog(array $logs): array
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
}