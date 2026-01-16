<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class Client
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
            $t->column('fname')->varchar()->index();
            $t->column('lname')->varchar()->index();
            $t->column('companyname')->varchar()->null();
            $t->column('username')->varchar()->unique()->index();
            $t->column('email')->varchar()->unique()->index();
            $t->column('hash')->text();
            $t->column('reset_token')->varchar()->null()->index();
            $t->column('email_verify_token')->varchar()->length(64)->null()->index();
            $t->column('token_expire')->datetime()->null();
            $t->column('status')->varchar()->length(25)->default('inactive')->index();
            $t->column('emailstatus')->enum(['verified','unverified'])->default('unverified')->index();
            $t->column('country')->varchar()->length(5)->index();
            $t->column('security')->bigint()->index();
            $t->column('created')->datetime()->index();
            $t->column('updated')->datetime()->null()->index();
        })->execute();
    }
}

