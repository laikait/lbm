<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class Price
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
            $t->column('relid')->bigint()->unsigned()->index();
            $t->column('type')->enum(['product','addon','new','renew','transfer','domainaddon','configoptions','usage'])->index();
            $t->column('payment_type')->enum(['free','onetime','recurring'])->index();
            $t->column('setup_fee')->decimal()->length('16,2')->unsigned()->default(0.00);
            $t->column('ot_fee')->decimal()->length('16,2')->unsigned()->default(0.00);
            $t->column('monthly_fee')->decimal()->length('16,2')->unsigned()->default(0.00);
            $t->column('quarterly_fee')->decimal()->length('16,2')->unsigned()->default(0.00);
            $t->column('semiannually_fee')->decimal()->length('16,2')->unsigned()->default(0.00);
            $t->column('annually_fee')->decimal()->length('16,2')->unsigned()->default(0.00);
            $t->column('created')->datetime()->index();
            $t->column('updated')->datetime()->null();
        })->execute();
    }
}

