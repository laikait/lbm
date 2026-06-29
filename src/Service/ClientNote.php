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
 * @method static array single(int $id)
 * @method static array getByClientId(int $relid, string $orderBy = 'ASC')
 * @method static array getByStaffId(int $relid, string $orderBy = 'ASC')
 * @method static array latest(?int $limit = null)
 * @method static ?array addNote(int|string $clientID)
 */
class ClientNote extends Relay
{
    protected static function getRelayAccessor(): string
    {
        return 'action.client.note';
    } 
}