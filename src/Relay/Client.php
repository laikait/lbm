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
 * @method static array limit()
 * @method static array single(int|string $entity)
 * @method static ?array update(int|string $entity)
 * @method static int count()
 * @method static int countByQuery()
 * @method static int countByStatus(string $status)
 * @method static int countCurrentMonth()
 * @method static array statusList()
 * @method static array statusAndColor()
 * @method static ?array addClient()
 */
class Client extends Relay
{
    protected static function getRelayAccessor(): string
    {
        return 'action.client';
    } 
}