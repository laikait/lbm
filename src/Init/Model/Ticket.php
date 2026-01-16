<?php

declare(strict_types=1);

// Namespace
namespace LBM\Init\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Blueprint;
use Laika\Model\Schema;

class Ticket
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
            $t->column('entity')->varchar();
            $t->column('subject')->text();
            $t->column('department')->int()->unsigned()->default(1)->index();
            $t->column('client')->bigint()->unsigned()->index();
            $t->column('staff')->bigint()->unsigned()->null()->index();
            $t->column('order')->bigint()->unsigned()->null()->index();
            $t->column('status')->varchar()->length(25)->default('open')->index();
            $t->column('creator_id')->bigint()->index();
            $t->column('creator_type')->enum(['staff','client'])->index();
            $t->column('created')->datetime()->index();
            $t->column('updated')->datetime()->null();
        })->execute();
    }
}

