<?php
/**
 * Laika PHP MVC Framework
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This file is part of the Laika PHP MVC Framework.
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

use Laika\Core\Auth\Auth;

/*=============================== ADMIN INFO ===============================*/
/**
 * Get Logged-in Staff Info
 * @return ?array
 */
function current_staff(): ?array
{
    return call_user_func([new Auth(ADMIN), 'user']);
}

/*================================= ACCESS =================================*/

/**
 * Check Staff Has Access
 * @param string $access Access Name. Example: 'product.read'
 */
function staff_has_access(string $access): bool
{
    $user = current_staff();
    $parts = explode('.', $access);
    $name = $parts[0];
    if (!isset($parts[1])) {
        return false;
    }
    $action = strtolower($parts[1]);
    return $user['permissions'][$name][$action] ?? false;
}

/**
 * Match Database Columns with Queries
 * @param array{string:mixed} $inputs Inputs From Request
 * @param array{string:string} $keyValuePair [query_key => db_column...]. Example: ['id' => 'note_id']
 * @return array
 */
function get_accepted_queries(array $inputs, array $keyValuePair): array
{
    $keys = [];
    // Get Accepted Query Values
    foreach($inputs as $k => $v) {
        if (in_array($k, array_keys($keyValuePair))) {
            $keys[$keyValuePair[$k]] = $v;
        }
    }
    return $keys;
}