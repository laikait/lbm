<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class LoginLogSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('login_logs', function (Blueprint $t) {
            $t->bigId('log_id');
            $t->enum('type', ['client', 'staff']);
            $t->unsignedBigInteger('relid')->comment('staffs/clients -> id');
            $t->string('ip_address', 100);
            $t->string('user_agent');
            $t->timestamp('logged_in_at');

            // Indexes
            $t->index('logged_in_at');
        });
    }
}
