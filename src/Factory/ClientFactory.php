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
     * Total Rows
     * @var int $total
     */
    private int $total;

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
        return $this->model->row($where, '=', 'OR')->status()->address('client')->result();
    }

    /**
     * Get Limit Clients
     */
    public function limit(): array
    {
        // Get Page Number
        $page = call_user_func([new Request, 'input'], 'page', 1);
        // Get Input
        $input = \do_hook('request.input', 'client');
        // Get Model Object
        $model = $this->model;
        // Get Model Object for Total Client
        $total = (new Client)->select($this->model->id);
        if (!empty($input)) {
            $input = "^{$input}";
            $where = [
                'fname' => $input,
                'lname' => $input,
                'username' => $input,
                'email' => $input,
                'status' => $input,
                'country' => $input,
                'companyname' => $input
            ];
            // Extend Client Model
            $model = $model->rows($where, 'REGEXP', 'OR', page:$page);
            // Extend Total Client Model
            $total = $total->where($where, 'REGEXP', 'OR');
        } else {
            // Extend Client Model
            $model = $model->rows($this->queries(), page:$page);
            // Extend Total Client Model
            $total = $total->where($this->queries());
        }

        // Set Total Client
        $this->total = $total->count();
        // Return Result
        return $model->status()->address('client')->result();
    }

    /**
     * Get Total Client
     * @return int
     */
    public function total(): int
    {
        return $this->total ?? 0;
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
        $inputs = \do_hook('request.inputs');
        // Get Accepted Query Values
        foreach($inputs as $k => $v) {
            if (in_array($k, $accepted)) {
                $queries[$k] = $v;
            }
        }
        return $queries;
    }
}