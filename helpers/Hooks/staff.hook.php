<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

/*============================= CLIENT HOOKS =============================*/
/**
 * Get Single Staff
 * @param int|string $entity Entity to Get Value.
 * @return array
 */
add_hook('staff.single', function (int|string $entity) {
    $entity = \htmlspecialchars($entity);
    $where = [
        'id'        =>  $entity,
        'uuid'      =>  $entity,
        'username'  =>  $entity,
        'email'     =>  $entity
    ];
});