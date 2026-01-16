<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class Sever
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
            $t->column('entity')->varchar()->index();
            $t->column('location')->varchar()->null();
            $t->column('ip')->varchar(100)->null();
            $t->column('extra_ips')->text()->null();
            $t->column('status_url')->varchar()->null();
            $t->column('nameservers')->varchar()->null();
            $t->column('account_limit')->int()->unsigned()->null();
            $t->column('secrets')->text()->null();
            $t->column('port')->int()->unsigned()->null();
            $t->column('status')->varchar(25)->index();
            $t->column('created')->datetime()->index();
            $t->column('updated')->datetime()->null();
        })->execute();
    }
}

