<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class Security
{
    /**
     * Migrate Table
     * @param string $table Table Name
     */
    public function init(string $table)
    {
        Schema::table($table)->create(function (Blueprint $t) {
            $t->column('id')->bigint()->auto();
            $t->column('uuid')->varchar()->length(50)->unique();
            $t->column('client_relid')->int()->unsigned()->null()->index();
            $t->column('staff_relid')->int()->unsigned()->null()->index();
            $t->column('entity')->varchar()->index();
            $t->column('serialize')->text();
        })->execute();
    }
}

