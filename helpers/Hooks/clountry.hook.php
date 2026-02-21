<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

use Laika\App\Model\Country;

/*============================= COUNTRY HOOKS =============================*/
/**
 * Get Countries
 * @return array
 */
add_hook('country.get', function (): array {
    return (new Country())->get();
});

/**
 * Get Single Country
 * @param int|string $entity Entity to Get Value.
 * @return array
 */
add_hook('country.single', function (int|string $entity): array {
    return (new Country())->where(['id' => $entity, 'code' => $entity], '=', 'OR')->first();
});

/**
 * Get Countries List
 * @return array
 */
add_hook('country.list', function (): array {
    $lists = do_hook('country.get');
    return array_column($lists, 'entity', 'code');
});