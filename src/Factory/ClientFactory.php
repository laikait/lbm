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
namespace CBM\Factory;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403) . die('403 Direct Access Denied!');

use Laika\App\Model\ClientStatusModel;
use CBM\Helper\Abstract\Factory;
use Laika\App\Model\ClientModel;
use Laika\Core\{Http\Request, Uri};

class ClientFactory extends Factory
{
    // Accepted Queries
    public static function accesptedQueries()
    {
        return [
            'id'            =>  'cid',
            'status'        =>  'status',
            'uid'           =>  'cuuid',
            'email'         =>  'email',
            'user'          =>  'username',
            'emailverified' =>  'email_verified',
            'city'          =>  'city',
            'state'         =>  'state',
            'zip'           =>  'zip',
            'country'       =>  'country_relid',
            'apistatus'     =>  'api_status'
        ];
    }

    // Queries
    public static function queries(): array
    {
        $exists = [];
        $uri = new Uri();
        $queries = $uri->queries();
        foreach($queries as $key => $val){
            $accepted = self::accesptedQueries();
            if(array_key_exists($key, $accepted) && Request::input($key)) $exists[$accepted[$key]] = $val;
        }
        return $exists;
    }

    public static function get(): array
    {
        // If Search By Post Request
        $client = Request::input('client');
        $model = new ClientModel;
        if ($client) {
            $client .= "%";
            $where = ['username'=>$client,'email'=>$client,'status'=>$client,'fname'=>$client,'lname'=>$client];
            $clients = $model->find($where, 'LIKE');
        } else {
            $clients = $model->limit(self::queries());
        }
        // Set Status Color And Format Date
        $statusModel = new ClientStatusModel();
        foreach ($clients as $key => $client) {
            $status = $statusModel->first(['status' => $client['status']]);
            $clients[$key]['status_color'] = $status['color'] ?? '#000000';
            $clients[$key]['created'] = apply_filter('date.show', $client['created']);
        }
        return $clients;
    }

    public static function update(array $data, array $where): int
    {
        return 1;
    }

    public static function count(string $column = null, array $where = [], string $operator = '=', string $compare = 'AND'): int
    {
        $model = new ClientModel;
        return $model->count($column, $where, $operator, $compare);
    }
}