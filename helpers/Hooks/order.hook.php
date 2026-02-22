<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

use Laika\App\Model\Order;
use Laika\App\Model\OrderStatus;

/*============================= INVOICE HOOKS =============================*/
/**
 * Get Single Order
 * @param int|string $entity Entity to Get Value.
 * @param ?Order $model Optional Order Model to Avoid Multiple Instantiation. Default is null.
 * @param string $select Columns to Select. Default is '*'.
 * @return array
 */
add_hook('order.single', function (int|string $entity, ?Order $model = null, string $select = '*') {
    return ($model ?? (new Order()))->select($select)->where(['id' => $entity, 'uid' => $entity, 'entity' => $entity], '=', 'OR')->first();
});

/**
 * Get Limit Orders
 * @return array
 */
add_hook('order.limit', function(string $asc = 'ASC'): array {
    // Get Input
    $input = \do_hook('request.input', 'invoice');
    // Invoice Model
    $model = new Order();

    // Get Model Object for Total Invoices
    $count = (new Order())->select($this->model->id);
    if (!empty($input)) {
        $input = "^{$input}";
        $where = [
            'entity' => $input,
            'status' => $input,
            'client' => $input
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

    // Get Page Number & Limit
    $page = (int) do_hook('request.input', 'page', 1);
    $limit = (int) do_hook('option.int', 'data.limit', 20);

    $invoices = $model->limit($limit)->offset($page)->order($model->id, $asc)->get();
    // Set Total Staff
    $total = $count->count();

    // Set Status Details
    $statusModel = new OrderStatus();
    array_filter($invoices, function ($invoice, $k) use ($statusModel, &$invoices) {
        $invoices[$k]['status'] = \do_hook('order.status', $invoice['status'], $statusModel);
    }, ARRAY_FILTER_USE_BOTH);

    return ['invoices' => $invoices, 'total' => $total];
}, 1000);

/**
 * Get Order Status Details
 * @param int|string $status Status.
 * @param ?OrderStatus $model Order Status Model Object.
 * @return array
 */
add_hook('order.status', function (string $entity, ?OrderStatus $model = null) {
    // Get Order Status
    return ($model ?? (new OrderStatus()))->select('entity,color')->where(['entity' => $entity])->first();
});