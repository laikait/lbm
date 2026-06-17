<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class TransactionSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('transactions', function (Blueprint $t) {
            $t->bigId('tx_id');
            $t->unsignedBigInteger('client_relid')->comment('clients -> cid');
            $t->unsignedBigInteger('invoice_relid')->nullable()->default(null)->comment('invoices -> invoice_id');
            $t->unsignedInteger('currency_relid')->comment('currencies -> currency_id');
            $t->unsignedBigInteger('gateway_relid')->nullable()->default(null)->comment('payment_gateways -> id');
            $t->string('transaction_ref')->nullable()->default(null)->comment('Gateway Tx ID');
            $t->decimal('amount', 18, 4);
            $t->decimal('fee', 18, 4)->default(0.0000);
            $t->decimal('exchange_rate', 18, 4)->default(1.0000);
            $t->enum('type', ['payment','refund','credit','chargeback','reversal'])->default('payment');
            $t->enum('status', ['pending','completed','failed','cancelled'])->default('pending');
            $t->string('description')->nullable()->default(null);
            $t->serialize('gateway_data')->nullable()->default(null);
            $t->timestamps('tx_created_at', 'tx_updated_at');

            // Indexes
            $t->index('client_relid');
            $t->index('invoice_relid');
            $t->index('currency_relid');
            $t->index('gateway_relid');
            $t->index('transaction_ref');
            $t->index('status');
            $t->index('tx_created_at');
            $t->index('tx_updated_at');
        });
    }
}
