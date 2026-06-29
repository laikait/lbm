<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class ProductGroupSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('product_groups', function (Blueprint $t) {
            $t->id('group_id');
            $t->string('group_slug');
            $t->string('group_name', 100);
            $t->text('description');
            $t->enum('is_active', ['yes', 'no'])->default('yes');
            $t->timestamps('group_created_at', 'group_updated_at');

            // Indexes
            $t->unique('group_slug');
            $t->unique('group_created_at');
        });
    }
}
