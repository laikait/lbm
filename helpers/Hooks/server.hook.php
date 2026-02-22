<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

use Laika\App\Model\Server;
use Laika\App\Model\ServerStatus;

/*============================= SERVER HOOKS =============================*/
/**
 * Get Servers
 * @return array
 */
add_hook('server.get', function (): array {
    $servers = (new Server())->get();
    $status_model = new ServerStatus();
    array_filter($servers, function ($server, $key) use ($status_model, &$servers) {
        $servers[$key]['status'] = do_hook('server.status', $server['status'], $status_model);
    }, ARRAY_FILTER_USE_BOTH);

    return $servers;
});

/**
 * Get Single Server
 * @param int|string $server Server id or uid or entity.
 * @return array
 */
add_hook('server.single', function (int|string $server): array {
    $server = (new Server())->where(['id' => $server, 'uid' => $server, 'entity' => $server], '=', 'OR')->first();
    $server['status'] = do_hook('server.status', $server['status']);
    return $server;
});

/**
 * Get Server Status
 * @param string $status Status. Example: active, inactive
 * @param ?ServerStatus $model ServerStatus Model Object.
 * @return array
 */
add_hook('server.status', function (string $status, ?ServerStatus $model = null): array {
    return ($model ?? (new ServerStatus()))->select('entity,color')->where(['entity' => $status])->first();
}, 1000);

/**
 * Get Server Status List
 * @return array
 */
add_hook('server.status.list', function (): array {
    $statuses = (new ServerStatus())->select('entity,color')->get();
    return array_column($statuses, 'color', 'entity');
}, 1000);