<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class Group
{
    /**
     * Migrate Table
     * @param string $table Table Name
     */
    public function init(string $table)
    {
        Schema::table($table)->create(function (Blueprint $t) {
            $t->column('id')->int()->unsigned()->auto();
            $t->column('uuid')->varchar()->length(50)->unique();
            $t->column('entity')->text();
            $t->column('headline')->text()->null();
            $t->column('details')->mediumtext()->null();
            $t->column('status')->varchar()->length(25)->index();
            $t->column('created')->datetime()->index();
            $t->column('updated')->datetime()->null()->index();
        })->execute();
    }
}

