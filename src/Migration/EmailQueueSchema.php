<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class EmailQueueSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('email_queue', function (Blueprint $t) {
            $t->bigId('queue_id');
            $t->unsignedBigInteger('client_relid')->comment('clients -> cid');
            $t->unsignedInteger('template_relid')->comment('email_templates -> et_id');
            $t->string('to_email', 150);
            $t->string('from_name', 150)->nullable()->default(null);
            $t->string('from_email', 150);
            $t->string('reply_to', 150)->nullable()->default(null);
            $t->string('subject');
            $t->longText('body_html');
            $t->longText('body_plain');
            $t->unsignedInteger('status_relid')->comment('email_queue_statuses -> status_id');
            $t->unsignedInteger('attempts')->default(0);
            $t->text('error_message')->nullable()->default(null);
            $t->timestamp('sent_at')->nullable()->default(null);
            $t->timestamp('queue_created_at');

            // Indexes
            $t->index('client_relid');
            $t->index('template_relid');
            $t->index('status_relid');
            $t->index('sent_at');
            $t->index('queue_created_at');
        });
    }
}
