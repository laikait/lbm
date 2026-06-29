<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class ServerGroupSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('server_groups', function (Blueprint $t) {
            $t->id('group_id');
            $t->string('group_name', 100);
            $t->enum('fill_type', ['sequentially','by_server','least_full'])->default('sequentially');
            $t->timestamp('group_created_at');

            $t->index('group_created_at');
        });
    }
}
