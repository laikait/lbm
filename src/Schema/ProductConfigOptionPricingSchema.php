<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class ProductConfigOptionPricingSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('product_config_option_pricing', function (Blueprint $t) {
            $t->id('pcop_id')->comment('Product Config Option Pricing ID');
            $t->unsignedInteger('pcos_relid')->comment('product_config_option_subs -> pcos_id');
            $t->unsignedInteger('currency_relid')->comment('currencies -> currency_id');
            $t->unsignedInteger('billing_cycle_relid');
            $t->decimal('setup_fee', 18, 4)->default(0.0000);
            $t->decimal('price', 18, 4)->default(0.0000);

            // Indexes
            $t->unique(['pcos_relid', 'currency_relid', 'billing_cycle_relid'], 'pp_config');
            $t->index('pcos_relid');
            $t->index('currency_relid');
            $t->index('billing_cycle_relid');
        });
    }
}
