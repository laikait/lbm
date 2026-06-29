<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class ProductConfigGroupSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('product_config_groups', function (Blueprint $t) {
            $t->id('pcg_id')->comment('Product Config Groups ID');
            $t->string('config_group_name', 100)->comment('e.g. ram_size, disk_size, dropdown');
            $t->text('description');
            $t->timestamp('pcg_created_at');

            // Indexes
            $t->unique('config_group_name');
            $t->index('pcg_created_at');
        });
    }
}
