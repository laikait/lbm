<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class ServiceUsageRecordSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('service_usage_records', function (Blueprint $t) {
            $t->bigId('sur_id')->comment('Service Usage Records ID');
            $t->unsignedBigInteger('service_relid')->comment('client_services -> service_id');
            $t->string('metric_name', 60);
            $t->unsignedBigInteger('quantity');
            $t->timestamp('recorded_at');
            $t->enum('billed', ['yes', 'no'])->default('no');

            // Indexes
            $t->index('billed');
            $t->index(['service_relid', 'billed'], 'sur_service_billed');
        });
    }

}
