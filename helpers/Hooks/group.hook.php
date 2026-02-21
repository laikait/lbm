<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

use Laika\App\Model\Group;
use Laika\App\Model\GroupStatus;

/*============================= GROUP HOOKS =============================*/
/**
 * Get Groups
 * @return array
 */
add_hook('group.get', function (): array {
    return (new Group())->get();
});

/**
 * Get Single Group
 * @param int|string $id Group id or uuid.
 * @return array
 */
add_hook('group.single', function (int|string $id): array {
    return (new Group())->where(['id' => $id, 'uuid' => $id], '=', 'OR')->first();
});

/**
 * Get Group Statuses
 * @return array
 */
add_hook('group.status.list', function (): array {
    $statuses = (new GroupStatus())->select('entity, color')->get();
    return array_column($statuses, 'color', 'entity');
});