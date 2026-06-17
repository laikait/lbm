<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Core\Exceptions\MigrationException;
use App\Model\OrderModel;
use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class OrderSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('orders', function (Blueprint $t) {
            $t->bigId('oid');
            $t->unsignedBigInteger('client_relid')->comment('clients -> cid');
            $t->unsignedBigInteger('invoice_relid')->nullable()->comment('invoices -> invoice_id');
            $t->unsignedInteger('promo_relid')->nullable()->comment('promo_codes -> promo_id');
            $t->unsignedInteger('status_relid')->default(1)->comment('order_statuses -> status_id');
            $t->unsignedInteger('currency_relid')->comment('currencies -> currency_id');
            $t->string('order_number', 100);
            $t->decimal('amount', 18, 4);
            $t->string('order_from_ip', 100)->nullable()->default(null);
            $t->decimal('fraud_score', 5, 2)->default(0.00);
            $t->timestamps('order_created_at', 'order_updated_at');

            // Indexes
            $t->index('client_relid');
            $t->index('invoice_relid');
            $t->index('promo_relid');
            $t->index('status_relid');
            $t->index('currency_relid');
            $t->unique('order_number');
            $t->index('order_created_at');
            $t->index('order_updated_at');
        });
    }

    /**
     * REMOVE
     * Default Values to Insert
     * @return void
     */
    public function default(): void
    {
        $model = new OrderModel();
        $model->transaction(function (OrderModel $m) {
            try {
                $m->insert([
                    'client_relid' => 1,
                    'invoice_relid' => 1,
                    'order_number' => 'ord-20251214c',
                    'status_relid' => 2,
                    'amount' => 25.0000,
                    'currency_relid' => 1,
                    'order_from_ip' => '::1'
                ]);
                $m->insert([
                    'client_relid' => 1,
                    'invoice_relid' => 1,
                    'order_number' => 'ord-20260209b',
                    'status_relid' => 1,
                    'amount' => 45.0000,
                    'currency_relid' => 1,
                    'order_from_ip' => '::1'
                ]);
            } catch (\Throwable $e) {
                throw new MigrationException("Unable to Insert Into orders. {$e->getMessage()}", (int) $e->getCode(), $e);
            }
        });
        return;
    }
}
