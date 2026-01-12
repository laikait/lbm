<?php

/**
 * Cloud Bill Master
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This Project Don't Provide Any Permission to Use it In Any Other Webapplication
 */

declare(strict_types=1);

// Namespace
namespace LBM\Factory;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403) . die('403 Direct Access Denied!');

use Laika\Core\Http\Request;
use Laika\App\Model\Client;

class ClientFactory
{
    /**
     * @var Client $model
     */
    private Client $model;

    /**
     * Initiate Client Factory
     */
    public function __construct()
    {
        $this->model = new Client();
    }

    /**
     * Get Single Client
     * @return array
     */
    public function single(int|string $client): array
    {
        $client = htmlspecialchars($client);
        $where = [
            'id'        =>  $client,
            'uuid'      =>  $client,
            'username'  =>  $client,
            'email'     =>  $client
        ];
        return $this->model->row($where, '=', 'OR')->status()->result();
    }

    /**
     * Get Limit Clients
     */
    public function limit(): array
    {
        // Get Page Number
        $page = call_user_func([new Request, 'input'], 'page', 1);
        return $this->model->rows($this->queries(), page:$page)->status()->result();
    }

    /**
     * Find Clients
     */
    public function find(): array
    {
        // Get Page Number
        return $this->model->rows($this->queries())->status()->result();
    }

    /*============================ INTERNAL API ============================*/
    /**
     * Match Database Columns with Queries
     * @return array
     */
    private function queries()
    {
        $accepted = ['id', 'uuid', 'fname', 'lname', 'username', 'email', 'status', 'country', 'companyname'];
        $queries = [];
        $inputs = call_user_func([new Request(), 'inputs']);
        // Get Accepted Query Values
        foreach($inputs as $k => $v) {
            if (in_array($k, $accepted)) {
                $queries[$k] = $v;
            }
        }
        return $queries;
    }
}