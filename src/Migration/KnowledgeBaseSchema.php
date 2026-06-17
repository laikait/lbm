<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class KnowledgeBaseSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('articles', function (Blueprint $t) {
            $t->bigId('kb_id');
            $t->unsignedInteger('category_relid')->nullable()->comment('kb_categories -> kb_cat_id');
            $t->unsignedBigInteger('staff_relid')->comment('staffs -> sid');
            $t->string('title');
            $t->string('slug');
            $t->longText('body');
            $t->unsignedInteger('views')->default(0);
            $t->unsignedInteger('votes_helpful')->default(0);
            $t->unsignedInteger('votes_unhelpful')->default(0);
            $t->enum('is_featured', ['yes', 'no'])->default('no');
            $t->enum('is_active', ['yes', 'no'])->default('yes');
            $t->timestamp('published_at');
            $t->timestamps('kb_created_at', 'kb_updated_at');

            // Indexes
            $t->index('category_relid');
            $t->index('staff_relid');
            $t->unique('slug');
            $t->index('is_featured');
            $t->index('is_active');
            $t->index('kb_created_at');
            $t->index('kb_updated_at');
        });
    }
}
