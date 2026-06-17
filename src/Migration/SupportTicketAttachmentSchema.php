<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class SupportTicketAttachmentSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('support_ticket_attachments', function (Blueprint $t) {
            $t->bigId('attachment_id');
            $t->unsignedBigInteger('reply_relid')->comment('support_ticket_replies -> reply_id');
            $t->string('filename');
            $t->string('path');
            $t->string('mime', 60)->nullable();
            $t->unsignedInteger('size')->nullable()->comment('In Byte');
            $t->timestamp('attachment_created_at');

            // Indexes
            $t->index('reply_relid');
            $t->index('attachment_created_at');
        });
    }
}
