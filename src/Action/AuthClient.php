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
use Laika\Session\Relay\Session;
use Laika\Core\Relay\Relays\Request;
use Laika\Core\Relay\Relays\Visitor;
use Laika\Core\Relay\Relays\Csrf;
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
        // Validate CSRF
        if (!Csrf::is_valid()) {
            return ['status' => false, 'message' => LANG::$invalidCsrf];
        }

        // Get Client
        $input = Request::input('user');

        // Client Columns To Get
        // EDIT LATER.
        // THIS METHOD NOT COMPLETED YET
        //##############################################################################################//
        $columns = ['cid', 'password', 'first_name', 'last_name', 'username', 'email', 'status_name'];

        $client = (new Client())->single($input, $columns);

        // Check Client Exists & Active
        if (empty($client) || ($client['status_name'] != 'active')) {
            return ['status' => false, 'message' => LANG::$invalidUser];
        }

        // Check Password is Valid
        $password = $client['password'];

        // Check Password is Valid
        if (!password_verify(Request::input('password'), $password)) {
            return ['status' => false, 'message' => LANG::$invalidUser];
        }

        // Unset Password
        unset($client['password']);

        try {
            // Set Auth User Data
            Auth::create($client);
            // Log Login
            $logs = [
                'type' => 'client',
                'relid' => $client['cid'],
                'ip_address' => Visitor::ip(),
                'user_agent' => Visitor::userAgent(),
            ];
            $this->createLog($logs);
            // Set Session ID & Type
            Session::set(["id" => bin2hex(random_bytes(6)) . $client['cid'], 'type' => PANEL]);
        } catch (\Throwable $th) {
            $message = do_hook('redirect.message', LANG::$generalError, $th->getMessage());
            return ['status' => false, 'message' => $message];
        }
        // Set Auth Session

        // Redirect to Dashboard
        return ['status' => true, 'message' => sprintf(LANG::$welcome, $client['first_name'])];
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