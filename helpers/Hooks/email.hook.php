<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

use Laika\App\Model\Email;

/*============================= EMAIL HOOKS =============================*/
/**
 * Get Emails
 * @return array
 */
add_hook('email.get', function (): array {
    return (new Email())->get();
});

/**
 * Emails List By Group
 * @return array
 */
add_hook('email.group.list', function (): array {
    $rows  = (new Email())->order('group')->get();
    $groups = [];
    foreach ($rows  as $row) {
        $group = $row['group'];
        unset($row['group']);

        $groups[$group][] = $row;
    }
    return $groups;
});

/**
 * Get Single Email
 * @param string $group Group Name.
 * @param string $action Action Name.
 * @return array
 */
add_hook('email.single', function (string $group, string $action): array {
    return (new Email())->where(['group' => $group, 'action' => $action])->first();
});