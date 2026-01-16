<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class Product
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
            $t->column('group')->varchar()->length(100)->index();
            $t->column('module')->varchar()->length(100)->index();
            $t->column('entity')->varchar()->length(500);
            $t->column('description')->text()->null();
            $t->column('type')->enum(['free','onetime','recurring'])->index();
            $t->column('status')->varchar()->length(25)->index();
            $t->column('service_type')->enum(['shared','reseller','server','other'])->index();
            $t->column('recurring_limit')->int()->unsigned();
            $t->column('prorata_billing')->enum(['yes','no'])->default('no');
            $t->column('billing_date')->int()->unsigned()->default(0);
            $t->column('auto_terminate')->enum(['yes','no'])->default('no');
            $t->column('termination_email')->int()->unsigned()->default(0);
            $t->column('created')->datetime()->index();
            $t->column('updated')->datetime()->null();
        })->execute();
    }
}

