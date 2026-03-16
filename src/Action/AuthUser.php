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

use Laika\Core\Auth\Auth;
use LBM\Support\Initiate;
use Laika\Session\Session;
use Laika\Core\Http\Request;
use Laika\Core\Http\Response;
use Laika\Core\Helper\Client;
use Laika\App\Model\LoginLogModel;

class AuthUser
{
    /** @var Request $request */
    protected Request $request;

    /** @var Response $response */
    protected Response $response;

    /** @var LoginLogModel $model */
    protected LoginLogModel $model;

    /** @var string $timezone */
    protected string $timezone;

    /** @var string $timeformat */
    protected string $timeformat;

    public function __construct(?Request $request = null, ?Response $response = null)
    {
        // Initiate Requirements
        $init = new Initiate();
        $init->common(ADMIN);

        // Assign Additionals
        $this->model = new LoginLogModel();
        $this->request = is_object($request) ? $request : new Request();
        $this->response = is_object($response) ? $response : new Response();
        $this->timezone = do_hook('option', 'time.zone', 'UTC');
        $this->timeformat = do_hook('option', 'datetime.format', 'Y-M-d H:i:s');
    }

    /**
     * Create Login Log
     * @param array $logs Logs to Insert
     * @return array
     */
    public function create_log(array $logs): array
    {
        return $this->model->transaction(function(LoginLogModel $m) use ($logs) {
            try {
                $m->insert($logs);
            } catch (\Throwable $th) {
                $message = \do_hook('redirect.message', \LANG::$logCreateFailed, $th->getMessage());
                return ['status' => false, 'message' => $message];
            }
            return ['status' => true, 'message' => \LANG::$logInSuccessful];
        });
    }

    /**
     * Staff Login
     * @return ?array
     */
    public function staff_login(): ?array
    {
        // Initiate User Auth
        $auth = new Auth(ADMIN);

        // Check Valid User Data Stored in Session & Database
        $random_id = Session::get("id", ADMIN);

        // Get Authenticated ID
        $len = strlen((string) $random_id);
        $id = $len === 0 ? 0 : (int) $random_id[$len -1];

        // Database Stored User
        $auth_user = $auth->user(ADMIN);

        // Return if Already Authenticated
        if (isset($auth_user['id']) && ($id === (int) $auth_user['id'])) {
            return ['status' => true, 'message' => \LANG::$authenticated];
        }

        // Process Login
        return $this->request->isPost() ? $this->processStaffLogin() : null;
    }

    /**
     * Panel Login
     * @return ?array
     */
    public function panel_login(): ?array
    {
        // Initiate User Auth
        $auth = new Auth(PANEL);

        // Check Valid User Data Stored in Session & Database
        $random_id = Session::get("id", PANEL);

        // Get Authenticated ID
        $len = strlen((string) $random_id);
        $id = $len === 0 ? 0 : (int) $random_id[$len -1];

        // Database Stored User
        $auth_user = $auth->user(PANEL);

        // Return if Already Authenticated
        if (isset($auth_user['id']) && ($id === (int) $auth_user['id'])) {
            return ['status' => true, 'message' => \LANG::$authenticated];
        }

        // Process Login
        return $this->request->isPost() ? $this->processPanelLogin() : null; // processPanelLogin Not Completed Yet
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
        $input = $this->request->input('user');
        // Staff Columns To Get
        $columns = ['id', 'password', 'role_name', 'permissions', 'first_name', 'last_name', 'username', 'email', 'status_name'];

        $staff = (new Staff())->single($input, $columns);

        // Check Staff Exists & Active
        if (empty($staff) || ($staff['status_name'] != 'active')) {
            return ['status' => false, 'message' => \LANG::$invalidUser];
        }

        // Check Password is Valid
        $password = $staff['password'];
        // Unset Password
        unset($staff['password']);
        // Check Password is Valid
        if (!password_verify($this->request->input('password'), $password)) {
            return ['status' => false, 'message' => \LANG::$invalidUser];
        }

        try {
            // Set Auth User Data
            call_user_func([new Auth(ADMIN), 'create'], $staff);
            // Log Login
            $client = new Client();
            $logs = [
                'type' => 'staff',
                'relid' => $staff['id'],
                'ip_address' => $client->ip(),
                'user_agent' => $client->userAgent(),
            ];
            call_user_func([$this, 'create_log'], $logs);
        } catch (\Throwable $th) {
            $message = \do_hook('redirect.message', \LANG::$generalError, $th->getMessage());
            return ['status' => false, 'message' => $message];
        }
        // Set Auth Session
        Session::set("id", bin2hex(random_bytes(6)) . $staff['id'], ADMIN);

        // Redirect to Dashboard
        return ['status' => true, 'message' => sprintf(\LANG::$welcome, $staff['first_name'])];
    }

    /**
     * Validate Staff Login
     * @return ?array
     */
    public function validateStaffLogin(): ?array
    {
        // Initiate User Auth
        $auth = new Auth(ADMIN);
        // Check Valid User Data Stored in Session & Database
        $random_id = Session::get("id", ADMIN);

        // Get Authenticated ID
        $len = strlen((string) $random_id);
        $id = $len === 0 ? 0 : (int) $random_id[$len -1];

        // Database Stored User
        $auth_user = $auth->user(ADMIN);

        // Redirect to Login Page if Not Authenticated
        if (empty($random_id) || !isset($auth_user['id']) || ($id == 0) || ((int) $auth_user['id'] !== $id)) {
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