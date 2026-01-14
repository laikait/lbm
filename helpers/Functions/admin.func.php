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

use Laika\Core\Helper\Auth;

/*=============================== ADMIN INFO ===============================*/
/**
 * Get Logged-in Staff Info
 */
function staff(): ?array
{
    return call_user_func([new Auth(ADMIN), 'user']);
}

/*================================= ACCESS =================================*/
/**
 * CHeck Admin Has Access
 * @param string $access Access Name. Example: 'product.read'
 */
function admin_access(string $access): bool // Complete it Later
{
    $user = staff();
    $parts = explode('.', $access);
    $name = $parts[0];
    $action = $parts[1] ?? 'unknown';
    return (isset($user['role']['entities'][$name][$action]) && $user['role']['entities'][$name][$action]);
}