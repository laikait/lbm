<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

use Laika\App\Model\Invoice;

/*============================= INVOICE HOOKS =============================*/
/**
 * Get Single Invoice
 * @param int|string $entity Entity to Get Value.
 * @return array
 */
add_hook('invoice.single', function (int|string $entity) {
    return (new Invoice())->where(['id' => $entity, 'entity' => $entity], '=', 'OR')->first();
});