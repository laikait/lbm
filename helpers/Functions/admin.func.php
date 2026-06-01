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

use LBM\Service\AuthStaff;
use Laika\Core\Service\Request;

/*=============================== ADMIN INFO ===============================*/
/**
 * Get Logged-in Staff Info
 * @return ?array
 */
function current_staff(): ?array
{
    $staff = null;
    if ($staff === null) $staff = AuthStaff::user();
    return $staff;
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
 * @param array<string:string> $keyValuePair [query_key => db_column...]. Example: ['id' => 'note_id']
 * @return array
 */
function query_to_columns(array $keyValuePair): array
{
    $keys = [];
    // Get Accepted Query Values
    foreach(Request::inputs() as $k => $v) {
        if (!$v) {
            continue;
        }
        if (in_array($k, array_keys($keyValuePair))) {
            $keys[$keyValuePair[$k]] = $v;
        }
    }
    return $keys;
}

/**
 * Make PI Chart From Data
 * @param array<int, array{label: string, total: int, color: string}> $data
 * Example: array(array('label'=>'paid', 'total'=>5, ));
 * @return array{circumf: float|int, offset: float|int, arc: array{color: string, dash: float|int, gap: float|int, offset: float|int}}
 */
function pi_chart(array $data) {
    $count = array_sum(array_column($data, 'total'));
    $circumf = 2 * M_PI * 50;
    $offset  = 0;
    $arcs = [];
    foreach ($data as $d) {
        $dash = (int) $d['total'] / $count * $circumf;
        $arcs[] = [
            'color'  => $d['color'],
            'dash'   => round($dash, 2),
            'gap'    => round($circumf - $dash, 2),
            'offset' => round(-$offset, 2),
        ];
        $offset += $dash;
    }
    return [
        'circumf' => $circumf,
        'offset' => $offset,
        'arc' => $arcs
    ];
};