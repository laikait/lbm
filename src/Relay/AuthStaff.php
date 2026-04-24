<?php
/**
 * Name: Laika Shield
 * Provider: Laika IT
 * Email: strblackhawk@gmail.com
 */

declare(strict_types=1);

namespace LBM\Relay;

use Laika\Core\Relay\Relay;

/**
 * @method static ?array login()
 * @method static ?array validate()
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