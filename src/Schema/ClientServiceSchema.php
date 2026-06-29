<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class ClientServiceSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('client_services', function (Blueprint $t) {
            $t->bigId('service_id');
            $t->unsignedBigInteger('client_relid')->comment('clients -> cid');
            $t->unsignedInteger('product_relid')->comment('products -> pid');
            $t->unsignedInteger('server_relid')->nullable()->default(null)->comment('servers -> server_id');
            $t->string('domain')->nullable();
            $t->string('username', 60)->nullable()->default(null);
            $t->string('password')->nullable()->comment('Encrypted');
            $t->unsignedInteger('billing_cycle_relid')->comment('billing_cycles -> billing_cycle_id');
            $t->unsignedInteger('currency_relid')->comment('currencies -> currency_id');
            $t->decimal('amount', 18, 4);
            $t->timestamp('next_due_date')->nullable()->default(null);
            $t->timestamp('registration_date')->nullable()->default(null);
            $t->timestamp('termination_date')->nullable()->default(null);
            $t->unsignedInteger('status_relid')->default(1)->comment('client_service_statuses -> status_id');
            $t->string('suspension_reason')->nullable()->default(null);
            $t->serialize('module_data')->nullable()->default(null)->comment('Serialized Module Data');
            $t->timestamps();

            // Indexes
            $t->index('client_relid');
            $t->index('product_relid');
            $t->index('server_relid');
            $t->index('domain');
            $t->index('currency_relid');
            $t->index('status_relid');
            $t->index('billing_cycle_relid');
            $t->index('next_due_date');
        });
    }
}
