<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class DomainNameserverSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('domain_nameservers', function (Blueprint $t) {
            $t->id('ns_id');
            $t->unsignedBigInteger('domain_relid')->comment('domains -> dimain_id');
            $t->string('hostname');
            $t->string('ip_address', 100)->nullable()->default(null);
            $t->timestamp('ns_created_at');

            // Indexes
            $t->index('domain_relid');
            $t->index('ns_created_at');
        });
    }
}
