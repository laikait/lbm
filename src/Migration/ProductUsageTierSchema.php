<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class ProductUsageTierSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('product_usage_tiers', function (Blueprint $t) {
            $t->id('pst_id')->comment('Product Usage Tiers ID');
            $t->unsignedInteger('product_relid')->comment('products -> id');
            $t->unsignedInteger('currency_relid')->comment('currencies -> currency_id');
            $t->string('metric_name', 60)->comment('e.g. "bandwidth_gb", "api_calls"');
            $t->unsignedBigInteger('units_from');
            $t->unsignedBigInteger('units_to')->nullable()->default(null)->comment('NULL = unlimited');
            $t->decimal('unit_price', 18, 8);
            $t->timestamp('pst_created_at');

            // Indexes
            $t->index('product_relid');
            $t->index('currency_relid');
            $t->index('pst_created_at');
        });
    }
}
