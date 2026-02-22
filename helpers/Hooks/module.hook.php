<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

use Laika\App\Model\Module;
use Laika\App\Model\ModuleStatus;

/*============================= MODULE HOOKS =============================*/
/**
 * Get Modules
 * @return array
 */
add_hook('module.get', function (): array {
    $modules = (new Module())->get();
    $status_model = new ModuleStatus();
    array_filter($modules, function ($module, $key) use ($status_model, &$modules) {
        $modules[$key]['status'] = do_hook('module.status', $module['status'], $status_model);
    }, ARRAY_FILTER_USE_BOTH);

    return $modules;
});

/**
 * Get Single Module
 * @param int|string $module Module id or uid or entity.
 * @return array
 */
add_hook('module.single', function (int|string $module): array {
    $module = (new Module())->where(['id' => $module, 'uid' => $module, 'entity' => $module], '=', 'OR')->first();
    $module['status'] = do_hook('module.status', $module['status']);
    return $module;
});

/**
 * Get Module Status
 * @param string $status Status. Example: active, inactive
 * @param ?ModuleStatus $model ModuleStatus Model Object.
 * @return array
 */
add_hook('module.status', function (string $status, ?ModuleStatus $model = null): array {
    return ($model ?? (new ModuleStatus()))->select('entity,color')->where(['entity' => $status])->first();
}, 1000);

/**
 * Get Module Status List
 * @return array
 */
add_hook('module.status.list', function (): array {
    $statuses = (new ModuleStatus())->select('entity,color')->get();
    return array_column($statuses, 'color', 'entity');
}, 1000);