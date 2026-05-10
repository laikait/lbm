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
 * @method static array single(int $id)
 * @method static array latest(?int $limit = null)
 * @method static array byType(string $type)
 * @method static array byTypeAndId(string $type, ?int $id = null)
 * @method static bool addActivity(array $data)
 */
class Activity extends Relay
{
    protected static function getRelayAccessor(): string
    {
        return 'action.activity';
    } 
}