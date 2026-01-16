<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class Invoice
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
            $t->column('amount')->decimal()->length('16,2');
            $t->column('serilize')->text()->null();
            $t->column('client_relid')->bigint()->unsigned()->index();
            $t->column('order_relid')->bigint()->unsigned()->null()->index();
            $t->column('status')->varchar()->length(25)->index();
            $t->column('due_date')->datetime()->null()->index();
            $t->column('created')->datetime()->index();
            $t->column('updated')->datetime()->null()->index();
        })->execute();
    }
}

