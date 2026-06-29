<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class ProductAddonMapSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('product_addon_map', function (Blueprint $t) {
            $t->unsignedInteger('product_relid')->comment('products -> pid');
            $t->unsignedInteger('addon_relid')->comment('product_addons -> addon_id');

            // Indexes
            $t->primary(['product_relid', 'addon_relid']);
        });
    }
}
