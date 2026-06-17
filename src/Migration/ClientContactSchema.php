<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Core\Exceptions\MigrationException;
use App\Model\ClientContactModel;
use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class ClientContactSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('client_contacts', function (Blueprint $t) {
            $t->bigId('cc_id')->comment('Client Contact ID');
            $t->unsignedBigInteger('client_relid')->comment('clients -> cid');
            $t->string('first_name', 80);
            $t->string('middle_name', 80)->nullable()->default(null);
            $t->string('last_name', 80);
            $t->string('email');
            $t->string('username', 80)->nullable()->comment('No Panel Access');
            $t->string('password')->nullable()->comment('No Panel Access');
            $t->string('phone_cc', 5)->nullable()->comment('Phone Calling Code');
            $t->string('phone_number', 30)->nullable();
            $t->string('street')->nullable();
            $t->string('city', 100)->nullable();
            $t->string('state', 100)->nullable();
            $t->string('postcode', 20)->nullable();
            $t->unsignedInteger('country_relid')->comment('countries -> country_id')->nullable();
            $t->unsignedInteger('status_relid')->default(1)->comment('client_statuses -> status_id');
            $t->serialize('permissions');
            $t->enum('is_primary', ['yes', 'no'])->default('no');
            $t->timestamps('cc_created_at', 'cc_updated_at');

            // Indexes
            $t->index('client_relid');
            $t->index('first_name');
            $t->index('last_name');
            $t->index('username');
            $t->index('email');
            $t->index('status_relid');
            $t->index('country_relid');
            $t->index('is_primary');
            $t->index('cc_created_at');
        });
    }

    /**
     * REMOVE
     * Default Values to Insert
     * @return void
     */
    public function default(): void
    {
        $model = new ClientContactModel();
        $model->transaction(function (ClientContactModel $m) {
            try {
                $m->insert([
                    'client_relid' => 1,
                    'first_name' => 'Contact',
                    'last_name' => '1',
                    'email' => 'testcontact1@test.com',
                    'username' => 'testcontact1',
                    'password' => password_hash('123456', PASSWORD_BCRYPT),
                    'phone_cc' => '+880',
                    'phone_number' => '01713271724',
                    'street' => '173/3, Senbari Road',
                    'city' => 'Mymensingh',
                    'status_relid' => 1,
                    'country_relid' => 17,
                    'permissions' => serialize(['contact' => ['create' => true, 'read' => true,'update' => true, 'delete' => true]]),
                    'is_primary' => 'yes'
                ]);
                $m->insert([
                    'client_relid' => 1,
                    'first_name' => 'Contact',
                    'middle_name' => 'Name',
                    'last_name' => '2',
                    'email' => 'testcontact2@test.com',
                    'username' => 'testcontact2',
                    'password' => password_hash('123456', PASSWORD_BCRYPT),
                    'phone_cc' => '+880',
                    'phone_number' => '01534004963',
                    'status_relid' => 1,
                    'country_relid' => 17,
                    'permissions' => serialize(['contact' => ['create' => true, 'read' => true,'update' => true, 'delete' => true]]),
                    'is_primary' => 'no'
                ]);
            } catch (\Throwable $e) {
                throw new MigrationException("Unable to Insert Into client_contacts. {$e->getMessage()}", (int) $e->getCode(), $e);
            }
        });
        return;
    }
}
