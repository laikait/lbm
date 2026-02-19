<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

use Laika\App\Model\Staff;
use Laika\App\Model\Client;

/*============================= CLIENT HOOKS =============================*/
/**
 * Get Note Staff
 * @param int|string $entity Entity to Get Value.
 * @return array
 */
add_hook('note.staff', function (int|string $id) {
    return (new Staff())->select('uuid,username,email')->where(['id' => (int) $id])->first();
});