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

use Laika\App\Model\ClientStatus;
use Laika\App\Model\ClientNote;
use Laika\Core\Http\Request;
use Laika\App\Model\Country;
use Laika\App\Model\Address;
use Laika\App\Model\Client;
use Laika\App\Model\Staff;
use LBM\Abstract\Factory;

class ClientFactory extends Factory
{
    /**
     * @var Client $model
     */
    protected Client $model;

    /**
     * Page Number
     * @var int $page
     */
    private int $page;

    /**
     * Data Limit
     * @var int $limit
     */
    private int $limit;

    /**
     * Total Rows
     * @var int $total
     */
    private int $total;

    /**
     * Accepted Queries
     * @var array $accepted
     */
    private array $accepted;

    /**
     * Initiate Client Factory
     */
    public function __construct()
    {
        $this->model = new Client();
        $this->page = (int) \call_user_func([new Request, 'input'], 'page', 1);
        $this->limit = \do_hook('option.int', 'data.limit', 20);
        $this->accepted = ['id', 'uuid', 'fname', 'lname', 'username', 'email', 'status', 'country', 'companyname'];
    }

    /**
     * Get Single Client
     * @param int|string $entity Entity to Get Value.
     * @return array
     */
    public function first(int|string $entity): array
    {
        $entity = \htmlspecialchars($entity);
        $where = [
            'id'        =>  $entity,
            'uuid'      =>  $entity,
            'username'  =>  $entity,
            'email'     =>  $entity
        ];

        // Get Client
        $client = $this->model->where($where, '=', 'OR')->first();

        // Get Other Related Values
        if (!empty($client)) {
            // Get Status
            $client['status'] = (new ClientStatus())->select('entity,color')->where(['entity' => $client['status']])->first();

            // Client Notes
            $client['notes'] = (new ClientNote())->select('note,staff,created')->where(['relid' => $client['id']])->get();

            // Get Address
            $client['address'] = (new Address())->select('address_1,address_2,city,state,zip,country')->where(['type' => 'client', 'relid' => $client['id']])->first();

            // Get Note Staffs
            $staff = new Staff();
            foreach ($client['notes'] as $k => $note) {
                $client['notes'][$k]['staff'] = $staff->select('uuid,username')->where(['id' => $note['staff']])->first();
            }
        }

        return $client;
    }

    /**
     * Get Limit Clients
     */
    public function limit(): array
    {
        // Get Input
        $input = \do_hook('request.input', 'client');

        // Get Model Object for Total Client
        $total = (new Client())->select($this->model->id);
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
            // Extend Total Client Model
            $total = $total->where($where, 'REGEXP', 'OR');
            // Extend Client Model
            $this->model = $this->model->where($where, 'REGEXP', 'OR');
        } else {
            // Extend Total Client Model
            $total = $total->where($this->queries());
            // Extend Client Model
            $this->model = $this->model->where($this->queries());
        }

        // Return Result
        $clients = $this->model->select()->limit($this->limit)->offset($this->page)->get();
        // Set Total Client
        $this->total = $total->count();

        // Get Related Values
        if (!empty($clients)) {
            $smodel = new ClientStatus();
            foreach ($clients as $k => $client) {
                // Get Status
                $clients[$k]['status'] = $smodel->select('entity,color')->where(['entity' => $client['status']])->first();
            }
        }

        return $clients;
    }

    /**
     * Get Statuses List
     * @return array
     */
    public function status_list(): array
    {
        return (new ClientStatus)->list();
    }

    /**
     * Get Countries List
     * @return array
     */
    public function country_list(): array
    {
        return (new Country())->list();
    }

    /**
     * Get Total Client
     * @return int
     */
    public function total(): int
    {
        return $this->total ?? 0;
    }
}