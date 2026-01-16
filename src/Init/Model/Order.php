<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class Order
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
            $t->column('entity')->varchar()->length(50)->index();
            $t->column('type')->varchar();
            $t->column('amount')->decimal()->length('16,2')->unsigned()->default(0.00);
            $t->column('next_invoice_amount')->decimal()->length('16,2')->unsigned()->null();
            $t->column('payment_method')->varchar()->null();
            $t->column('status')->varchar()->length(25)->index();
            $t->column('nameservers')->varchar()->null();
            $t->column('json')->text()->null();
            $t->column('client')->bigint()->unsigned()->index();
            $t->column('promocode')->varchar()->null();
            $t->column('promotype')->varchar()->null();
            $t->column('promovalue')->varchar()->null();
            $t->column('created')->datetime()->index();
            $t->column('updated')->datetime()->null();
            $t->column('ip')->varchar()->length(100)->null();
        })->execute();
    }
}

