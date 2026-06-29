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
 * @method static array limit(string|array|null $columns = null)
 * @method static array single(int|string $entity, array $columns)
 * @method static ?array update(int|string $entity)
 * @method static int count()
 */
class Support extends Relay
{
    protected static function getRelayAccessor(): string
    {
        return 'action.support';
    } 
}