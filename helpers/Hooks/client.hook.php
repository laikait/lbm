<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

use Laika\App\Model\Staff;
use Laika\App\Model\Client;
use Laika\App\Model\Address;
use LBM\Factory\ClientFactory;
use Laika\App\Model\ClientNote;
use Laika\App\Model\ClientStatus;

/*============================= CLIENT HOOKS =============================*/
/**
 * Get Limit Clients
 * @return array
 */
add_hook('client.limit', function(): array {
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
    $result['clients'] = $model->select()->limit($limit)->offset($page)->get();
    // Set Total Client
    $result['total'] = $total->count();

    // Get Related Values
    if (!empty($result)) {
        $smodel = new ClientStatus();
        foreach ($result['clients'] as $k => $client) {
            // Get Status
            $result['clients'][$k]['status'] = $smodel->select('entity,color')->where(['entity' => $client['status']])->first();
        }
    }
    return $result;
});

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
    // Client Model
    $model = new Client();

    // Get Client
    $result = $model->where($where, '=', 'OR')->first();

    // Get Other Related Values
    if (!empty($result)) {
        // Get Status
        $result['status'] = (new ClientStatus())->select('entity,color')->where(['entity' => $result['status']])->first();

        // Client Notes
        $result['notes'] = (new ClientNote())->select('note,staff,created')->where(['relid' => $result['id']])->order('id', 'DESC')->get();

        // Get Address
        $result['address'] = (new Address())->select('address_1,address_2,city,state,zip,country')->where(['type' => 'client', 'relid' => $result['id']])->first();

        // Get Note Staffs
        $staff = new Staff();
        foreach ($result['notes'] as $k => $note) {
            $result['notes'][$k]['staff'] = $staff->select('uuid,username')->where(['id' => $note['staff']])->first();
        }
    }

    return $result;
});