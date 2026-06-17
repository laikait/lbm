<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class AnnouncementSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('announcements', function (Blueprint $t) {
            $t->bigId('id');
            $t->unsignedBigInteger('staff_relid')->comment('staffs -> id');
            $t->string('title');
            $t->longText('body');
            $t->timestamp('published_at');
            $t->enum('is_active', ['yes', 'no'])->default('yes');
            $t->timestamp('created_at');

            // Indexes
            $t->index('staff_relid');
            $t->index('is_active');
        });
    }
}
