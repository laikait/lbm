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
 * @method static array validate(string $input):
 */
class PasswordValidator extends Relay
{
    protected static function getRelayAccessor(): string
    {
        return 'support.password.validator';
    } 
}