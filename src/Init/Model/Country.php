<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class Country
{
    /**
     * Migrate Table
     * @param string $table Table Name
     */
    public function init(string $table)
    {
        Schema::table($table)->create(function (Blueprint $t) {
            $t->column('id')->int()->unsigned()->auto();
            $t->column('code')->varchar()->length(3);
            $t->column('entity')->varchar()->length(50);
            $t->column('phonecode')->varchar()->length(5);
        })->execute();
    }
}

