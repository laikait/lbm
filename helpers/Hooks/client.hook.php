<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

use Laika\App\Model\Staff;
use Laika\App\Model\Client;
use LBM\Factory\ClientFactory;
use Laika\App\Model\ClientNote;
use Laika\App\Model\ClientStatus;
use Laika\App\Model\ClientActivity;

/*============================= CLIENT HOOKS =============================*/
/**
 * Get Limit Clients
 * @return array
 */
add_hook('client.limit', function(string $asc = 'ASC'): array {
    // Get Input
    $input = do_hook('request.input', 'client');
    $model = new Client();
    $factory = new ClientFactory();

    // Get Model Object for Total Clients
    $total = (new Client())->select($model->id);
    if (!empty($input)) {
        $input = "^{$input}";
        $where = [
            'fname' => $input,
            'lname' => $input,
            'username' => $input,
            'email' => $input,
            'status' => $input,
            'country' => $input,
            'companyname' => $input
        ];
        // Extend Total Client Model
        $total = $total->where($where, 'REGEXP', 'OR');
        // Extend Client Model
        $model = $model->where($where, 'REGEXP', 'OR');
    } else {
        // Extend Total Client Model
        $total = $total->where($factory->queries());
        // Extend Client Model
        $model = $model->where($factory->queries());
    }

    // Get Page Number & Limit
    $page = (int) do_hook('request.input', 'page', 1);
    $limit = (int) do_hook('option.int', 'data.limit', 20);

    // Return Result
    $result['clients'] = $model->limit($limit)->offset($page)->order($model->id, $asc)->get();

    // Set Total Client
    $result['total'] = $total->count();

    // Set Status Details
    $statusModel = new ClientStatus();
    array_filter($result['clients'], function ($res, $k) use ($statusModel, $result) {
        $result['clients'][$k]['status'] = \do_hook('client.status', $res['status'], $statusModel);
    }, ARRAY_FILTER_USE_BOTH);

    return $result;
}, 1000);

/**
 * Get Single Client
 * @param int|string $entity Entity to Get Value.
 * @return array
 */
add_hook('client.single', function(int|string $entity): array {
    $entity = \htmlspecialchars($entity);
    $where = [
        'id'        =>  $entity,
        'uuid'      =>  $entity,
        'username'  =>  $entity,
        'email'     =>  $entity
    ];
    // Return Client
    return (new Client())->where($where, '=', 'OR')->first();
}, 1000);

/**
 * Get Client Notes
 * @param int|string $relid Client RelId
 * @param string $order Order By Column.
 * @param string $order Order By ASC/DESC. Default is DESC
 * @return array
 */
add_hook('client.notes', function (int|string $relid, string $column = 'id', string $order = 'DESC'): array {
    $notes = (new ClientNote())->select('staff,title,note,created')->where(['relid' => (int) $relid])->order($column, $order)->get();
    $staff_model = new Staff();
    foreach ($notes as $k => $note) {
        $notes[$k]['staff'] = $staff_model->select('uuid,username')->where(['id' => $note['staff']])->first();
    }
    return $notes;
}, 1000);

/**
 * Get Client Status
 * @param string $entity Client Status Entity
 * @param ?ClientStatus $model Optional ClientStatus Model to Avoid Multiple Instantiation. Default is null.
 * @return array
 */
add_hook('client.status', function (string $entity, ?ClientStatus $model = null): array {
    return ($model ?? (new ClientStatus()))->select('entity,color')->where(['entity' => $entity])->first();
}, 1000);

/**
 * Get Client Statuses
 * @return array
 */
add_hook('client.status.list', function (): array {
    $statuses = (new ClientStatus())->select('entity,color')->get();
    return array_column($statuses, 'color', 'entity');
}, 1000);

/**
 * Get Client Limit Activities
 * @param int|string $relid Client Entity
 * @param string $column Column to Order By. Default is id
 * @param string $order Order By ASC/DESC. Default is DESC
 * @throws InvalidArgumentException
 * @return array
 */
add_hook('client.activities.limit', function (int|string $relid, string $column = 'id', string $order = 'DESC'): array {
    // Get Activities
    $activities = (new ClientActivity())->where(['relid' => (int) $relid])->order($column, $order)->get();
    foreach ($activities as $k => $activity) {
        $activities[$k]['changes'] = unserialize($activity['changes']);
    }
    return $activities;
}, 1000);