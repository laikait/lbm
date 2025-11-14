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
            if(array_key_exists($key, $accepted) && Request::instance()->input($key)) $exists[$accepted[$key]] = $val;
        }
        return $exists;
    }

    public static function get(): array
    {
        // If Search By Post Request
        $search = Request::instance()->input('client');
        $model = new ClientModel;
        if ($search) {
            $search .= "%";
            $where = ['username'=>$search,'email'=>$search,'status'=>$search,'fname'=>$search,'lname'=>$search];
            $clients = $model->find($where, 'LIKE');
        } else {
            $clients = $model->limit((int) Request::instance()->input('page'), self::queries());
        }
        // Set Status Color And Format Date
        return self::colorAndDate($clients, new ClientStatusModel());
    }

    public static function update(array $data, array $where): int
    {
        return 1;
    }

    public static function single(int|string $id): array
    {
        $column = match (true) {
            is_numeric($id) =>  'cid',
            filter_var($id, FILTER_VALIDATE_EMAIL) => 'email',
            str_starts_with($id, 'uuid') => 'cuuid',
            default =>  'username'
        };

        $model = new ClientModel;
        $client = $model->first([$column => $id]);
        return self::colorAndDate($client, new ClientStatusModel());
    }

    public static function count(?string $column = null, array $where = [], string $operator = '=', string $compare = 'AND'): int
    {
        $model = new ClientModel;
        return $model->count($column, $where, $operator, $compare);
    }
}