<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class ClientServiceNoteSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('client_service_notes', function (Blueprint $t) {
            $t->bigId('note_id');
            $t->unsignedBigInteger('service_relid')->comment('client_services -> service_id');
            $t->unsignedBigInteger('staff_relid')->comment('staffs -> sid');
            $t->text('note');
            $t->timestamp('cs_created_at');

            // Indexes
            $t->index('service_relid');
            $t->index('staff_relid');
            $t->index('cs_created_at');
        });
    }
}
