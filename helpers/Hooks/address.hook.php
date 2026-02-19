<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

use Laika\App\Model\Address;

/*============================= CLIENT HOOKS =============================*/
/**
 * Get Addresses
 * @param int|string $relid RelId. Example: 1, 2, 3
 * @param string $type Type. Example: staff, client
 * @return array
 */
add_hook('address.get', function (int|string $relid, string $type): array {
    if (!in_array($type, ['staff', 'client'])) {
        return [];
    }
    return (new Address())->where(['relid' => (int) $relid, 'type' => $type])->get();
});

/**
 * Get Profile Address
 * @param int|string $relid RelId. Example: 1, 2, 3
 * @param string $type Type. Example: staff, client
 * @return array
 */
add_hook('address.profile', function (int|string $relid, string $type): array {
    if (!in_array($type, ['staff', 'client'])) {
        return [];
    }
    return (new Address())->where(['relid' => (int) $relid, 'type' => $type, 'profile_default' => 'yes'])->first();
});

/**
 * Get Billing Address
 * @param int|string $relid RelId. Example: 1, 2, 3
 * @param string $type Type. Example: staff, client
 * @return array
 */
add_hook('address.billing', function (int|string $relid, string $type): array {
    if (!in_array($type, ['staff', 'client'])) {
        return [];
    }
    return (new Address())->where(['relid' => (int) $relid, 'type' => $type, 'billing_default' => 'yes'])->first();
});

/**
 * Get Contact Address
 * @param int|string $relid RelId. Example: 1, 2, 3
 * @param string $type Type. Example: staff, client
 * @return array
 */
add_hook('address.contact', function (int|string $relid, string $type): array {
    if (!in_array($type, ['staff', 'client'])) {
        return [];
    }
    return (new Address())->where(['relid' => (int) $relid, 'type' => $type, 'contact_default' => 'yes'])->first();
});