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
 * @method static void common(?string $user_type = null)
 */
class Initiate extends Relay
{
    protected static function getRelayAccessor(): string
    {
        return 'support.initiate';
    } 
}