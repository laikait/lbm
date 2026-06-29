<?php
/**
 * Name: Laika Shield
 * Provider: Laika IT
 * Email: strblackhawk@gmail.com
 */

declare(strict_types=1);

namespace LBM\Service;

use Laika\Relay\Relay;

/**
 * @method static void AuthInit()
 * @method static ?array user()
 * @method static void destroy()
 */
class AuthStaff extends Relay
{
    protected static function getRelayAccessor(): string
    {
        return 'action.auth.staff';
    } 
}