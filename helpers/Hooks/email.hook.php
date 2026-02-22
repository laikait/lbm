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
add_hook('email.get', function (string $column = '*'): array {
    return (new Email())->select($column)->get();
});

/**
 * Emails List By Group
 * @return array
 */
add_hook('email.group.list', function (): array {
    $rows  = (new Email())->order('group')->get();
    $groups = [];
    array_filter($rows, function ($row) use (&$groups) {
        $groups[strtolower($row['group'])][] = ['title' => $row['title'], 'group' => $row['group']];
    });
    return $groups;
});

/**
 * Get Single Email
 * @param string $entity Group Name.
 * @return array
 */
add_hook('email.single', function (int|string $entity): array {
    return (new Email())->where(['id' => $entity, 'group' => $entity], '=', 'OR')->first();
});