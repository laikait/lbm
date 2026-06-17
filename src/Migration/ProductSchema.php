<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class ProductSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('products', function (Blueprint $t) {
            $t->id('pid');
            $t->string('product_slug');
            $t->unsignedInteger('group_relid')->comment('product_groups -> group_id');
            $t->string('product_name');
            $t->text('description')->nullable()->default(null);
            $t->unsignedInteger('type_relid')->comment('product_types -> product_type_id');
            $t->enum('pricing_model', ['one_time','recurring','usage','free'])
                ->default('recurring')
                ->comment('one_time, recurring, usage, free');
            $t->decimal('setup_fee', 18, 6)->default(0.0000);
            $t->decimal('tax_rate', 7, 4)->default(0.0000)->comment('% Applied');
            $t->unsignedInteger('welcome_email_relid')->nullable()->comment('email_templates -> et_id');
            $t->string('module_name', 100)->nullable()->comment('Provisioning Module');
            $t->serialize('module_config')->nullable()->comment('Server Config Params');
            $t->enum('stock_control', ['yes', 'no'])->default('no');
            $t->unsignedInteger('stock_qty')->nullable();
            $t->enum('is_featured', ['yes', 'no'])->default('no');
            $t->unsignedInteger('status_relid')->comment('product_statuses -> status_id');
            $t->timestamps('product_created_at', 'product_updated_at');

            // Indexes
            $t->unique('product_slug');
            $t->index('welcome_email_relid');
            $t->index('status_relid');
            $t->index('group_relid');
            $t->index('type_relid');
            $t->index('product_created_at');
            $t->index('product_updated_at');
        });
    }
}
