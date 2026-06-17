<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class InvoiceNoteSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('invoice_notes', function (Blueprint $t) {
            $t->bigId('note_id');
            $t->unsignedBigInteger('invoice_relid')->comment('invoices -> invoice_id');
            $t->unsignedBigInteger('staff_relid')->comment('staffs -> sid');
            $t->text('note');
            $t->timestamp('note_created_at');

            // Indexes
            $t->index('invoice_relid');
            $t->index('staff_relid');
            $t->index('note_created_at');
        });
    }
}
