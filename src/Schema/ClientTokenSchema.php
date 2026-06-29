<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class ClientTokenSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('client_tokens', function (Blueprint $t) {
            $t->bigId('token_id');
            $t->string('token');
            $t->enum('type', ['api','password_reset','email_verify','two_factor', 'support']);
            $t->unsignedBigInteger('client_relid')->nullable()->comment('clients -> cid');
            $t->unsignedBigInteger('client_contact_relid')->nullable()->comment('client_contacts -> cc_id');
            $t->timestamp('expires')->nullable()->default(null);
            $t->timestamp('last_used')->nullable()->default(null);
            $t->timestamp('token_created_at');
            
            // Indexes
            $t->index('token');
            $t->index('type');
            $t->index('client_relid');
            $t->index('client_contact_relid');
            $t->index('token_created_at');
        });
    }
}
