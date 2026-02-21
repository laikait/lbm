<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

use Laika\App\Model\Staff;
use Laika\App\Model\StaffRole;
use Laika\App\Model\StaffStatus;

/*============================= STAFF HOOKS =============================*/
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

    // Get Staff
    return (new Staff())->where($where, '=', 'OR')->first();
});

/**
 * Get Limit Clients
 * @return array
 */
add_hook('staff.limit', function(string $asc = 'ASC'): array {
    // Get Input
    $input = \do_hook('request.input', 'staff');
    // Staff Model
    $model = new Staff();

    // Get Model Object for Total Staffs
    $count = (new Staff())->select($this->model->id);
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
        $count = $count->where($this->queries());
        // Extend Staff Model
        $model = $model->where($this->queries());
    }

    // Return Result
    // Get Page Number & Limit
    $page = (int) do_hook('request.input', 'page', 1);
    $limit = (int) do_hook('option.int', 'data.limit', 20);
    $staffs = $model->limit($limit)->offset($page)->order($model->id, $asc)->get();
    // Set Total Staff
    $total = $count->count();

    // Get Related Values
    if (!empty($staffs)) {
        $statusModel = new StaffStatus();
        foreach ($staffs as $k => $staff) {
            // Set Status Details
            $staffs[$k]['status'] = \do_hook('staff.status', $staff['status'], $statusModel);
        }
    }

    return ['staffs' => $staffs, 'total' => $total];
}, 1000);

/**
 * Staff Role
 * @param string $role Role to Get Value. Example: admin, staff, etc.
 * @return array
 */
add_hook('staff.role', function (string $role) {
    $result = (new StaffRole())->where(['role' => $role])->first();
    $returl['entities'] = unserialize($result['entities']);
    return $returl;
});

/**
 * Staff Status
 * @param string $status Status to Get Value. Example: active, inactive, etc.
 * @param ?StaffStatus $model Optional StaffStatus Model to Avoid Multiple Instantiation. Default is null.
 * @return array
 */
add_hook('staff.status', function (string $status, ?StaffStatus $model = null) {
    return ($model ??(new StaffStatus()))->select('entity,color')->where(['entity' => $status])->first();
});
