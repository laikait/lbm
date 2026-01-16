<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class LoginLog
{
    /**
     * Migrate Table
     * @param string $table Table Name
     */
    public function init(string $table)
    {
        Schema::table($table)->create(function (Blueprint $t) {
            $t->column('id')->bigint()->unsigned()->auto();
            $t->column('uuid')->varchar()->length(50)->unique();
            $t->column('aid')->bigint()->unsigned()->null()->index();
            $t->column('cid')->bigint()->unsigned()->null()->index();
            $t->column('time')->datetime()->index();
            $t->column('ip')->varchar()->length(100);
            $t->column('browser')->varchar()->length(25);
            $t->column('session')->varchar()->length(50);
        })->execute();
    }
}

