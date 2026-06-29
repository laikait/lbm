<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class SupportTicketTagSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('support_ticket_tags', function (Blueprint $t) {
            $t->id('tag_id');
            $t->unsignedBigInteger('ticket_relid')->comment('support_tickets -> ticket_id');
            $t->string('tag', 60);

            // Index
            $t->unique(['ticket_relid', 'tag'], 'ticket_tag');
        });
    }
}
