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
 * @method static array     limit()
 * @method static array     single(int|string $entity)
 * @method static array     clientOrders(int $client_relid)
 * @method static int       count()
 * @method static int       countByQuery()
 * @method static array     statusList()
 */
class Order extends Relay
{
    protected static function getRelayAccessor(): string
    {
        return 'action.order';
    } 
}