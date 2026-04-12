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
 * @method static array single(int $id, array $columns)
 * @method static array latest(?int $limit = null)
 */
class Activity extends Relay
{
    protected static function getRelayAccessor(): string
    {
        return 'action.activity';
    } 
}