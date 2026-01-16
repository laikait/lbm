<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class Staff
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
            $t->column('role')->varchar()->length(50)->index();
            $t->column('fname')->varchar()->index();
            $t->column('lname')->varchar()->index();
            $t->column('username')->varchar()->unique();
            $t->column('email')->varchar()->unique();
            $t->column('hash')->text();
            $t->column('reset_token')->varchar()->null()->unique();
            $t->column('reset_expire')->datetime()->null();
            $t->column('emailstatus')->enum(['verified','unverified'])->default('unverified')->index();
            $t->column('status')->varchar()->length(25)->index();
            $t->column('security')->bigint()->index();
            $t->column('created')->datetime()->index();
            $t->column('updated')->datetime()->null();
        })->execute();
    }
}

