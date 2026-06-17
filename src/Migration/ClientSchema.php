<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class ClientSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('clients', function (Blueprint $t) {
            $t->bigId('cid');
            $t->string('company_name')->nullable();
            $t->string('first_name', 80);
            $t->string('middle_name', 80)->nullable();
            $t->string('last_name', 80);
            $t->string('email');
            $t->string('username', 80);
            $t->string('password');
            $t->string('phone_cc', 5)->nullable()->comment('Phone Calling Code');
            $t->string('phone_number', 30)->nullable();
            $t->string('street')->nullable();
            $t->string('city', 100)->nullable();
            $t->string('state', 100)->nullable();
            $t->string('postcode', 20)->nullable();
            $t->unsignedInteger('country_relid')->comment('countries -> country_id')->nullable();
            $t->unsignedInteger('currency_relid')->comment('currencies -> currency_id')->nullable();
            $t->unsignedInteger('status_relid')->default(2)->comment('client_statuses -> status_id');
            $t->timestamp('email_verified_at')->nullable()->default(null);
            $t->decimal('credit_balance', 18, 4)->default(0.0000);
            $t->enum('tax_exempt', ['yes', 'no'])->default('yes');
            $t->string('tax_id')->nullable()->comment('VAT / GST / EIN etc');
            $t->timestamp('last_login_at')->nullable()->default(null);
            $t->string('last_login_ip', 100)->nullable()->default(null);
            $t->timestamps('client_created_at', 'client_updated_at');

            $t->index('first_name');
            $t->index('last_name');
            $t->unique('username');
            $t->unique('email');
            $t->index('country_relid');
            $t->index('currency_relid');
            $t->index('status_relid');
            $t->index('client_created_at');
            $t->index('client_updated_at');
        });
    }
}
