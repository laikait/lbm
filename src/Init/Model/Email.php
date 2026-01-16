<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class Email
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
            $t->column('type')->varchar()->length(50)->index();
            $t->column('entity')->varchar();
            $t->column('body')->mediumtext();
            $t->column('from_name')->varchar();
            $t->column('from_email')->varchar();
            $t->column('system_default')->enum(['yes','no'])->default('no');
        })->execute();
    }
}

