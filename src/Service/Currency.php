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
 * @method static array     list() Get All Currencies
 * @method static array     single(int|string $entity) Get Single Currency From id or code
 * @method static array     default() Get Default Currency
 * @method static string    get_exchange_rate(int|string $from, int|string $to)
 * @method static string    convert(int|float|string $amount, int|string $from, int|string $to)
 */
class Currency extends Relay
{
    protected static function getRelayAccessor(): string
    {
        return 'action.currency';
    } 
}