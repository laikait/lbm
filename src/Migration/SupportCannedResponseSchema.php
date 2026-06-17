<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class SupportCannedResponseSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('support_canned_responses', function (Blueprint $t) {
            $t->id('response_id');
            $t->unsignedInteger('department_relid')->nullable()->comment('NULL = all departments');
            $t->string('title');
            $t->text('message');
            $t->unsignedBigInteger('staff_relid')->nullable()->comment('staffs -> sid');
            $t->timestamps('response_created_at', 'response_updated_at');

            // Indexes
            $t->index('department_relid');
            $t->index('staff_relid');
            $t->index('response_created_at');
            $t->index('response_updated_at');
        });
    }
}
