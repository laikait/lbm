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
 * @method static array latest(?int $limit = null)
 * @method static array single(int $entity, array $columns)
 * @method static array countPerStatus()
 */
class Invoice extends Relay
{
    protected static function getRelayAccessor(): string
    {
        return 'action.client.contact';
    } 
}