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

use Laika\Core\Relay\Relays\Redirect;
use Laika\Core\Relay\Relays\Request;
use Laika\Core\Relay\Relays\Visitor;
use LBM\Exception\ActionException;
use Laika\Core\Relay\Relays\Auth;
use Laika\Session\Relay\Session;
use App\Model\LoginLogModel;
use LANG;

class AuthStaff
{
    public function __construct()
    {
        // Initiate Requirements
        Auth::setType(ADMIN)->init();
    }

    ######################################################################################
    /*================================== EXTERNAL API ==================================*/
    ######################################################################################
    /**
     * Staff Login
     * @return void
     */
    public function login(): void
    {
        // Check Valid User Data Stored in Session & Database
        $id = (int) substr(Session::get("id", ''), 12);
        $type = Session::get("type");

        // Database Stored User
        $auth_user = Auth::user();

        // Return if Already Authenticated
        if (isset($auth_user['sid']) && ($id > 0) && ($id === (int) $auth_user['sid']) && ($type === ADMIN)) {
            Redirect::with(LANG::$authenticated, true)->to('staff.dashboard');
        }

        // Process Login
        if (Request::isPost()) $this->process();
    }

    /**
     * Validate Staff Login
     * @return void
     */
    public function validate(): void
    {
        // Get Authenticated ID
        $id = (int) substr(Session::get('id', ''), 12);
        $type = Session::get("type");

        // Authenticated User
        $user = $this->user();

        // Redirect to Login Page if Not Authenticated
        if (!isset($user['sid']) || ($id < 1) || ((int) $user['sid'] !== $id) || ($type !== ADMIN)) {
            // Destroy Previous Data if Exists
            Auth::destroy();
            // Remove Staff Data if Exists
            Session::end();
            Redirect::with(LANG::$unauthenticated, false)->to('staff.login');
        }
    }

    /**
     * Authenticated User Info
     * @return ?array
     */
    public function user(): ?array
    {
        return Auth::user();
    }

    /**
     * Destroy Authentication
     * @return void
     */
    public function destroy(): void
    {
        Auth::destroy();
    }

    ######################################################################################
    /*================================== INTERNAL API ==================================*/
    ######################################################################################
    /**
     * Process Staff Login
     * @return void
     */
    protected function process(): void
    {
        // Get Staff
        $input = Request::input('user');

        // Staff Columns To Get
        $columns = ['sid', 'password', 'role_name', 'permissions', 'first_name', 'last_name', 'username', 'email', 'status_name'];

        $staff = (new Staff())->single($input, $columns);

        // Check Staff Exists & Active
        if (empty($staff) || ($staff['status_name'] != 'active')) {
            do_hook('alert.set', LANG::$invalidUser, false);
            return;
        }

        // Check Password is Valid
        $password = $staff['password'];

        // Check Password is Valid
        if (!password_verify(Request::input('password'), $password)) {
            do_hook('alert.set', LANG::$invalidUser, false);
            return;
        }

        // Unset Password
        unset($staff['password']);

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
            // Set Auth Session ID & Type
            Session::set(["id" => bin2hex(random_bytes(6)) . $staff['sid'], 'type' => ADMIN]);
        } catch (\Throwable $th) {
            if (config('env', 'debug')) {
                throw new ActionException($th->getMessage(), (int) $th->getCode(), $th);
            }
            do_hook('alert.set', LANG::$generalError, false);
            return;
        }

        // Redirect to Dashboard
        Redirect::with(LANG::$welcome, true)->to('staff.dashboard');
    }

    /**
     * Create Login Log
     * @param array $logs Logs to Insert
     * @return array
     */
    protected function createLog(array $logs): array
    {
        return (new LoginLogModel())->transaction(function(LoginLogModel $m) use ($logs) {
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