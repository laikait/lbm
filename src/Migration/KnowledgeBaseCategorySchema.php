<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class KnowledgeBaseCategorySchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('kb_categories', function (Blueprint $t) {
            $t->id('kb_cat_id');
            $t->unsignedInteger('parent_id')->nullable()->comment('self -> kb_cat_id');
            $t->string('name');
            $t->string('slug');
            $t->enum('is_active', ['yes', 'no'])->default('yes');

            // Indexes
            $t->index('parent_id');
            $t->unique('slug');
            $t->index('is_active');
        });
    }
}
