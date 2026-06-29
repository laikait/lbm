<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class CreditNoteSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('credit_notes', function (Blueprint $t) {
            $t->bigId('credit_note_id');
            $t->unsignedBigInteger('client_relid');
            $t->unsignedInteger('currency_relid');
            $t->decimal('amount', 18, 4);
            $t->decimal('used_amount', 18, 4)->default(0.0000);
            $t->text('reason')->nullable()->default(null);
            $t->enum('status', ['open','partial','used','voided'])->default('open');
            $t->timestamp('credit_created_at');

            // Indexes
            $t->index('client_relid');
            $t->index('currency_relid');
            $t->index('credit_created_at');
        });
    }
}
