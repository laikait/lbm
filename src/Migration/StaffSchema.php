<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Core\Exceptions\MigrationException;
use App\Model\StaffModel;
use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class StaffSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('staffs', function (Blueprint $t) {
            $t->bigId('sid');
            $t->unsignedInteger('role_relid')->comment('staff_roles -> role_id');
            $t->string('first_name', 80);
            $t->string('middle_name', 80)->nullable()->default(null);
            $t->string('last_name', 80);
            $t->string('username', 80);
            $t->string('email');
            $t->string('password');
            $t->string('two_factor_secret')->nullable()->default(null);
            $t->timestamp('last_login_at')->nullable()->default(null);
            $t->string('last_login_ip', 100)->nullable()->default(null);
            $t->unsignedInteger('status_relid')->comment('staff_statuses -> status_id');
            $t->timestamps('staff_created_at', 'staff_updated_at');

            $t->unique('username');
            $t->unique('email');
            $t->index('role_relid');
            $t->index('status_relid');
            $t->index('staff_created_at');
            $t->index('staff_updated_at');
        });
    }

    /**
     * Default Values to Insert
     * @return void
     */
    public function default(): void
    {
        $model = new StaffModel();
        $model->transaction(function (StaffModel $m) {
            try {
                $statuses = [
                    'role_relid' => 1,
                    'first_name' => 'Showket',
                    'last_name' => 'Ahmed',
                    'username' => 'riyadhtayf',
                    'email' => 'riyadhtayf@gmail.com',
                    'password' => password_hash('123456', PASSWORD_ARGON2ID),
                    'status_relid' => 1
                ];
                $m->insert($statuses);
            } catch (\Throwable $e) {
                throw new MigrationException("Unable to Insert Default Into 'staffs. {$e->getMessage()}", (int) $e->getCode(), $e);
            }
        });
        return;
    }
}
