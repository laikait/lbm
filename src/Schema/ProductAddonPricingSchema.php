<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class ProductAddonPricingSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('product_addon_pricing', function (Blueprint $t) {
            $t->id('pap_id')->comment('Product Addon Pricing ID');
            $t->unsignedInteger('addon_relid')->comment('product_addons -> addon_id');
            $t->unsignedInteger('currency_relid')->comment('currencies -> currency_id');
            $t->unsignedInteger('billing_cycle_relid');
            $t->decimal('addon_price', 18, 4)->default(0.0000);
            
            // Indexes
            $t->unique(['addon_relid', 'currency_relid', 'billing_cycle_relid'], 'addon_pricing');
        });
    }
}
