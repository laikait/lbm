<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

use Laika\App\Model\Staff;
use LBM\Factory\StaffFactory;
use Laika\App\Model\LoginLog;
use Laika\App\Model\StaffNote;
use Laika\App\Model\StaffRole;
use Laika\App\Model\StaffStatus;
use Laika\App\Model\StaffActivity;

/*============================= STAFF HOOKS =============================*/
/**
 * Get Single Staff
 * @param int|string $entity Entity to Get Value.
 * @param ?Staff $model Optional Staff Model to Avoid Multiple Instantiation. Default is null.
 * @return array
 */
add_hook('staff.single', function (int|string $entity, ?Staff $model = null, string $select = '*') {
    $entity = \htmlspecialchars($entity);
    $where = [
        'id'        =>  $entity,
        'uid'      =>  $entity,
        'username'  =>  $entity,
        'email'     =>  $entity
    ];

    // Get Staff
    return ($model ?? (new Staff()))->select($select)->where($where, '=', 'OR')->first();
});

/**
 * Get Limit Clients
 * @return array
 */
add_hook('staff.limit', function(string $asc = 'ASC', string $select = '*'): array {
    // Get Input
    $input = \do_hook('request.input', 'staff');
    // Staff Model
    $model = (new Staff())->select($select);
    // Staff Factory
    $factory = new StaffFactory();

    // Get Model Object for Total Staffs
    $count = (new Staff())->select($model->id);
    if (!empty($input)) {
        $input = "^{$input}";
        $where = [
            'fname' => $input,
            'lname' => $input,
            'username' => $input,
            'email' => $input,
            'status' => $input
        ];
        // Extend Total Staff Model
        $count = $count->where($where, 'REGEXP', 'OR');
        // Extend Staff Model
        $model = $model->where($where, 'REGEXP', 'OR');
    } else {
        // Extend Total Staff Model
        $count = $count->where($factory->queries());
        // Extend Staff Model
        $model = $model->where($factory->queries());
    }

    // Get Page Number & Limit
    $page = (int) do_hook('request.input', 'page', 1);
    $limit = (int) do_hook('option.int', 'data.limit', 20);

    // Get Staffs
    $staffs = $model->limit($limit)->offset($page)->order($model->id, $asc)->get();
    
    // Set Total Staff
    $total = $count->count();
    
    // Set Status Details
    $statusModel = new StaffStatus();
    array_filter($staffs, function ($staff, $k) use ($statusModel, &$staffs) {
        $staffs[$k]['status'] = \do_hook('staff.status', $staff['status'], $statusModel);
    }, ARRAY_FILTER_USE_BOTH);
        
    // Return Result
    return ['staffs' => $staffs, 'total' => $total];
}, 1000);

/**
 * Staff Role
 * @param string $role Role to Get Value. Example: admin, staff, etc.
 * @param string $select Select Columns. Default is '*'.
 * @return array
 */
add_hook('staff.role', function (string $role, string $select = '*') {
    return (new StaffRole())->select($select)->where(['entity' => $role])->first();
});

/**
 * Staff Status
 * @param string $status Status to Get Value. Example: active, inactive, etc.
 * @param ?StaffStatus $model Optional StaffStatus Model to Avoid Multiple Instantiation. Default is null.
 * @return array
 */
add_hook('staff.status', function (string $status, ?StaffStatus $model = null) {
    return ($model ?? (new StaffStatus()))->select('entity,color')->where(['entity' => $status])->first();
});

/**
 * Get Staff Statuses
 * @return array
 */
add_hook('staff.status.list', function (): array {
    $statuses = (new StaffStatus())->select('entity,color')->get();
    return array_column($statuses, 'color', 'entity');
}, 1000);

/**
 * Get Staff Limit Activities
 * @param int|string $relid Staff Entity
 * @param string $column Column to Order By. Default is id
 * @param string $order Order By ASC/DESC. Default is DESC
 * @throws \InvalidArgumentException
 * @return array
 */
add_hook('staff.activities.limit', function (int|string $relid, string $column = 'id', string $order = 'DESC'): array {
    // Get Activities
    $activities = (new StaffActivity())->where(['relid' => (int) $relid])->order($column, $order)->get();
    foreach ($activities as $k => $activity) {
        $activities[$k]['changes'] = unserialize($activity['changes']);
    }
    return $activities;
}, 1000);

/**
 * Get Staff Notes
 * @param int|string $relid Staff RelId
 * @param string $order Order By Column.
 * @param string $order Order By ASC/DESC. Default is DESC
 * @return array
 */
add_hook('staff.notes', function (int|string $relid, string $column = 'id', string $order = 'DESC'): array {
    $notes = (new StaffNote())->select('staff,title,note,created')->where(['relid' => (int) $relid])->order($column, $order)->get();
    $staff_model = new Staff();
    foreach ($notes as $k => $note) {
        $notes[$k]['staff'] = $staff_model->select('uid,username')->where(['id' => $note['staff']])->first();
    }
    return $notes;
}, 1000);

/**
 * Get Staff Login Logs
 * @param int|string $relid Staff RelID
 * @return array
 */
add_hook('staff.login.logs', function (int|string $relid): array {
    return (new LoginLog())->where(['type' => 'staff', 'relid' => $relid])->order('id', 'DESC')->get();
}, 1000);