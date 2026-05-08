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

use Laika\Core\Service\Auth;
use Laika\Session\Relay\Session;
use Laika\Core\Service\Request;
use Laika\Core\Service\Redirect;
use Laika\Core\Service\Visitor;
use Laika\Core\Service\Csrf;
use App\Model\LoginLogModel;
use LANG;

class AuthClient
{
    public function __construct()
    {
        // Initiate Requirements
        Auth::setType(PANEL)->init();
    }

    ######################################################################################
    /*================================== EXTERNAL API ==================================*/
    ######################################################################################
    /**
     * Client Login
     * @return ?array
     */
    public function login(): ?array
    {
        // Check Valid User Data Stored in Session & Database
        $id = (int) substr(Session::get('id', ''), 12);
        $type = Session::get("type");

        // Authenticated User
        $user = $this->user();

        // Return if Already Authenticated
        if (isset($user['sid']) && ($id > 0) && ($id === (int) $user['sid']) && ($type === PANEL)) {
            return ['status' => true, 'message' => LANG::$authenticated];
        }

        // Process Login
        return Request::isPost() ? $this->process() : null;
    }

    /**
     * Validate Client Login
     * @return array
     */
    public function validate(): array
    {
        // Get Authenticated ID
        $id = (int) substr(Session::get('id', ''), 12);
        $type = Session::get("type");

        // Database Stored User
        $user = Auth::user();

        // Redirect to Login Page if Not Authenticated
        if (!isset($user['cid']) || ($id < 1) || ((int) $user['cid'] !== $id) || ($type !== PANEL)) {
            // Destroy Previous Data if Exists
            Auth::destroy();
            // Remove Client Data if Exists
            Session::end();
            return ['status' => false, 'message' => LANG::$unauthenticated];
        }
        return ['status' => true, 'message' => LANG::$authenticated];
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
     * Process Client Login
     * @return ?array
     */
    protected function process(): ?array
    {
        // Get Staff
        $input = Request::input('user');

        // Staff Columns To Get
        $columns = ['sid', 'password', 'role_name', 'permissions', 'first_name', 'last_name', 'username', 'email', 'status_name'];

        $staff = (new Staff())->single($input, $columns);

        // Check Staff Exists & Active
        if (empty($staff) || ($staff['status_name'] != 'active')) {
            alert_set(LANG::$invalidUser, false);
            return;
        }

        // Check Password is Valid
        $password = $staff['password'];

        // Check Password is Valid
        if (!password_verify(Request::input('password'), $password)) {
            alert_set(LANG::$invalidUser, false);
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
            alert_set(LANG::$generalError, false);
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
                return ['status' => false, 'message' => LANG::$logCreateFailed];
            }
            return ['status' => true, 'message' => LANG::$logInSuccessful];
        });
    }
}