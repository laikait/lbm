<?php
/**
 * Name: Laika Shield
 * Provider: Laika IT
 * Email: strblackhawk@gmail.com
 */

declare(strict_types=1);

namespace LBM\Service;

use Laika\Core\Relay\Relay;

/**
 * @method static array single(int|string $entity, array $columns)
 * @method static array list()
 */
class Country extends Relay
{
    protected static function getRelayAccessor(): string
    {
        return 'action.country';
    } 
}