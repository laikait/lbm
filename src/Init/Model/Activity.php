<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class Activity
{
    /**
     * Migrate Table
     * @param string $table Table Name
     */
    public function init(string $table)
    {
        Schema::table($table)->create(function (Blueprint $t) {
            $t->column('id')->bigint()->unsigned()->auto();
            $t->column('relid')->bigint()->unsigned()->index();
            $t->column('task')->varchar();
            $t->column('activity')->varchar()->length(1000);
            $t->column('changes')->text();
            $t->column('created')->datetime()->index();
        })->execute();
    }
}

