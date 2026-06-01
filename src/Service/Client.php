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
 * @method static array limit()
 * @method static array single(int|string $entity)
 * @method static ?array update(int|string $entity)
 * @method static int count()
 * @method static int countByQuery()
 * @method static int countByStatus(string $status)
 * @method static int countCurrentMonth()
 * @method static array statusList()
 * @method static ?array addClient()
 * @method static ?array modifyClient(int $cid)
 * @method static ?array resetPasswordByStaff(int $cid)
 */
class Client extends Relay
{
    protected static function getRelayAccessor(): string
    {
        return 'action.client';
    } 
}