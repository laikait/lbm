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
 * @method static array     limit()
 * @method static array     single(int|string $entity)
 * @method static array     latest(?int $limit = null)
 * @method static array     groupByStatus()
 * @method static string    totalSpentByClient(int $client_relid)
 * @method static string    totalOutstandingByClient(int $client_relid)
 * @method static array     clientInvoices(int $client_relid)
 */
class Invoice extends Relay
{
    protected static function getRelayAccessor(): string
    {
        return 'action.invoice';
    } 
}