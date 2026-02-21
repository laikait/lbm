<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

use Laika\App\Model\Currency;

/*============================= CURRENCY HOOKS =============================*/
/**
 * Get Currency List
 * @return array
 */
add_hook('currency.get', function(): array {
    return (new Currency())->get();
});

/**
 * Get Default Currency
 * @return array
 */
add_hook('currency.defult', function(): array {
    return (new Currency())->where(['system_default' => 'yes'], '=', 'OR')->first();
});

/**
 * Get Single Currency
 * @param int|string $entity Entity to Get Value.
 * @return array
 */
add_hook('currency.single', function(int|string $entity): array {
    return (new Currency())->where(['id' => $entity, 'code' => $entity], '=', 'OR')->first();
});