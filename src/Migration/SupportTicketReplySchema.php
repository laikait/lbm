<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class SupportTicketReplySchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('support_ticket_replies', function (Blueprint $t) {
            $t->bigId('reply_id');
            $t->unsignedBigInteger('ticket_relid')->comment('support_tickets -> ticket_id');
            $t->enum('author_type', ['client','staff','system'])->comment('client, staff, system');
            $t->unsignedBigInteger('author_relid')->nullable()->comment('NULL if author is system');
            $t->longText('message');
            $t->enum('is_internal', ['yes', 'no'])->default('no')->comment('staff-only note');
            $t->timestamp('reply_created_at');

            // Indexes
            $t->index('ticket_relid');
            $t->index(['author_type', 'author_relid'], 'reply_created_by');
            $t->index('is_internal');
            $t->index('reply_created_at');
        });
    }
}
