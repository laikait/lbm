<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class ProductAddonSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('product_addons', function (Blueprint $t) {
            $t->id('addon_id')->comment('Product Addon ID');
            $t->string('addon_name', 150);
            $t->text('description');
            $t->enum('pricing_model', ['one_time','recurring'])->default('recurring');
            $t->enum('is_active', ['yes', 'no'])->default('yes');
            $t->timestamps('addon_created_at', 'addon_updated_at');

            // Indexes
            $t->unique('addon_name');
            $t->index('is_active');
            $t->index('addon_created_at');
            $t->index('addon_updated_at');
        });
    }
}
