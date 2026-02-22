<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

use Laika\App\Model\Client;
use Laika\App\Model\Invoice;
use Laika\App\Model\InvoiceStatus;

/*============================= INVOICE HOOKS =============================*/
/**
 * Get Single Invoice
 * @param int|string $entity Entity to Get Value.
 * @return array
 */
add_hook('invoice.single', function (int|string $entity, ?Invoice $model = null, string $select = '*') {
    return ($model ?? (new Invoice()))->select($select)->where(['id' => $entity, 'uid' => $entity, 'entity' => $entity], '=', 'OR')->first();
});

/**
 * Get Limit Invoices
 * @return array
 */
add_hook('invoice.limit', function(string $asc = 'ASC'): array {
    // Get Input
    $input = \do_hook('request.input', 'invoice');
    // Invoice Model
    $model = new Invoice();

    // Get Model Object for Total Invoices
    $count = (new Invoice())->select($this->model->id);
    if (!empty($input)) {
        $input = "^{$input}";
        $where = [
            'client' => $input,
            'order' => $input,
            'entity' => $input,
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

    // Get Page Number & Limit
    $page = (int) do_hook('request.input', 'page', 1);
    $limit = (int) do_hook('option.int', 'data.limit', 20);

    $invoices = $model->limit($limit)->offset($page)->order($model->id, $asc)->get();
    // Set Total Staff
    $total = $count->count();

    // Set Other Details
    $statusModel = new InvoiceStatus();
    $clientModel = new Client();
    array_filter($invoices, function ($invoice, $k) use ($statusModel, $clientModel, &$invoices) {
        $invoices[$k]['status'] = \do_hook('invoice.status', $invoice['status'], $statusModel);
        $invoices[$k]['client'] = \do_hook('client.single', $invoice['client'], $clientModel, 'uid,username');
    }, ARRAY_FILTER_USE_BOTH);

    return ['invoices' => $invoices, 'total' => $total];
}, 1000);

/**
 * Get Invoice Status Details
 * @param int|string $status Status.
 * @param ?InvoiceStatus $model Invoice Status Model Object.
 * @return array
 */
add_hook('invoice.status', function (string $entity, ?InvoiceStatus $model = null) {
    // Get Invoice Status
    return ($model ?? (new InvoiceStatus()))->select('entity,color')->where(['entity' => $entity])->first();
});

/**
 * Get Invoice Status List
 * @return array
 */
add_hook('invoice.status.list', function (): array {
    $statuses = (new InvoiceStatus())->select('entity,color')->get();
    return array_column($statuses, 'color', 'entity');
}, 1000);