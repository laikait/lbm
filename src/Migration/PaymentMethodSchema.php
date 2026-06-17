<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class PaymentMethodSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('payment_methods', function (Blueprint $t) {
            $t->id('pm_id');
            $t->unsignedBigInteger('client_relid')->comment('clients -> cid');
            $t->unsignedInteger('gateway_relid')->comment('payment_gateways -> gateway_id');
            $t->unsignedInteger('type_relid')->comment('payment_method_types -> pm_type_id');
            $t->string('token')->comment('Gateway Vault Token');
            $t->char('last_four', 4)->nullable();
            $t->string('card_brand', 20)->nullable();
            $t->tinyInteger('expiry_month')->nullable();
            $t->tinyInteger('expiry_year')->nullable();
            $t->enum('is_default', ['yes', 'no'])->default('no');
            $t->string('billing_name')->nullable();
            $t->json('billing_address')->nullable();
            $t->timestamp('pm_created_at');

            // Indexes
            $t->index('client_relid');
            $t->index('gateway_relid');
            $t->index('type_relid');
            $t->index('is_default');
            $t->index('pm_created_at');
        });
    }
}
