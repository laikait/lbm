<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class DomainContactSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('domain_contacts', function (Blueprint $t) {
            $t->bigId('dc_id');
            $t->unsignedBigInteger('domain_relid')->comment('domains -> id');
            $t->enum('type', ['registrant','admin','tech','billing', 'abuse']);
            $t->string('company_name')->nullable();
            $t->string('first_name', 80)->nullable();
            $t->string('last_name', 80)->nullable();
            $t->string('email', 80)->nullable();
            $t->string('phone_cc', 30)->nullable()->comment('Phone Calling Code');
            $t->string('phone_number', 30)->nullable();
            $t->string('address1')->nullable();
            $t->string('address2')->nullable();
            $t->string('city', 100)->nullable();
            $t->string('state', 100)->nullable();
            $t->string('postcode', 20)->nullable();
            $t->unsignedInteger('country_relid')->comment('countries -> country_id');

            // Indexes
            $t->index('domain_relid');
            $t->index('country_relid');
        });
    }
}
