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
use Laika\App\Model\Staff;
use LBM\Abstract\Factory;

/*-------------- NOT USING ----------------*/
class StaffFactory extends Factory
{
    private static string $columns = 'id, uuid, role, fname, lname, username, email, status';

    // Accepted Queries
    protected static array $accepted = ['id', 'status', 'uuid','email', 'username', 'emailstatus', 'country'];

    /**
     * Get Accepted & Exists Queries
     * @return array
     */
    public static function queries(): array
    {
        $queries = [];
        $inputs = call_user_func([new Request, 'inputs']);

        array_filter($inputs, function($v, $k) use ($queries){
            if (in_array($k, self::$accepted)) {
                $queries[$k] = $v;
            }
        }, ARRAY_FILTER_USE_BOTH);

        return $queries;
    }

    public static function get(?string $columns = null): array
    {
        $columns = $columns ?: self::$columns;
        $page = call_user_func([new Request, 'input'], 'page', 1);
        $model = new Staff;
        return $model->rows(self::queries(), $columns, page:$page)
                    ->status('ClientStatus', 'status,entity')
                    ->result();
    }

    public static function search(?string $columns = null): array
    {
        $columns = $columns ?: self::$columns;
        $queries = self::queries();
        $page = call_user_func([new Request, 'input'], 'page', 1);
        $model = new Staff;
        return $model->rows($queries, $columns, compare:'OR', page:$page)
                    ->status('StaffStatus', 'status,entity')
                    ->result();
    }

    public static function update(array $where, array $data): int
    {
        return 1;
    }

    public static function first(int|string $entity, ?string $columns = null): array
    {
        $columns = $columns ?: self::$columns;
        $where = [
            'id'        =>  $entity,
            'uuid'      =>  $entity,
            'username'  =>  $entity,
            'email'     =>  $entity
        ];

        return (new Staff)->row($where, $columns)->status('ClientStatus', 'status,entity')->result();
    }

    public static function count(array $where = [], string $operator = '=', string $compare = 'AND'): int
    {
        $model = new Staff;
        return $model->select($model->id)->where($where, $operator, $compare)->count();
    }
}