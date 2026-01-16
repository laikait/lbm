<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class Address
{
    /**
     * Migrate Table
     * @param string $table Table Name
     */
    public function init(string $table)
    {
        Schema::table($table)->create(function (Blueprint $t) {
            $t->column('id')->bigint()->unsigned()->auto();
            $t->column('address_1')->varchar();
            $t->column('address_2')->null()->varchar();
            $t->column('city')->varchar()->null()->index();
            $t->column('zip')->varchar()->null()->index();
            $t->column('country')->varchar()->index();
            $t->column('type')->enum(['staff','client'])->index();
            $t->column('relid')->bigint()->unsigned()->index();
            $t->column('profile_default')->enum(['yes','no'])->default('no')->index();
            $t->column('billing_default')->enum(['yes','no'])->default('no')->index();
            $t->column('contact_default')->enum(['yes','no'])->default('no')->index();
            $t->column('created')->datetime()->index();
            $t->column('updated')->datetime()->null()->index();
        })->execute();
    }
}

