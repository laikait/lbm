<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class Promo
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
            $t->column('code')->varchar()->length(50)->index();
            $t->column('type')->enum(['percent','fixed','override'])->index();
            $t->column('status')->varchar()->length(25)->index();
            $t->column('value')->decimal()->length('16,2')->unsigned();
            $t->column('products')->varchar()->length(500);
            $t->column('require')->varchar()->length(500);
            $t->column('require_existing')->enum(['yes','no'])->default('no');
            $t->column('start_date')->datetime();
            $t->column('expire_date')->datetime()->null();
            $t->column('cycles')->varchar();
            $t->column('limits')->int()->unsigned()->default(0);
            $t->column('used')->int()->unsigned();
            $t->column('on_upgrade')->enum(['yes','no'])->default('no');
            $t->column('on_downgrade')->enum(['yes','no'])->default('no');
            $t->column('orderonce')->enum(['yes','no'])->default('no');
            $t->column('clientonce')->enum(['yes','no'])->default('no');
            $t->column('newclient')->enum(['yes','no'])->default('no');
            $t->column('existingclient')->enum(['yes','no'])->default('no');
            $t->column('recurring_invoice')->int()->default(0);
            $t->column('upgrage_config')->mediumtext()->null();
            $t->column('downgrade_config')->mediumtext()->null();
            $t->column('created')->datetime()->index();
            $t->column('updated')->datetime()->null();
        })->execute();
    }
}

