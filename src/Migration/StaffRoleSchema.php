<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Core\Exceptions\MigrationException;
use App\Model\StaffRoleModel;
use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class StaffRoleSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('staff_roles', function (Blueprint $t) {
            $t->id('role_id');
            $t->string('role_name', 50)->comment('Role Name');
            $t->serialize('permissions')->comment('Serialized Data');
            $t->timestamps('role_created_at', 'role_updated_at');

            // Indexes
            $t->index('role_name');
            $t->index('role_created_at');
            $t->index('role_updated_at');
        });
    }

    /**
     * Default Values to Insert
     * @return void
     */
    public function default(): void
    {
        $model = new StaffRoleModel();
        $model->transaction(function (StaffRoleModel $m) {
            $permissions = [
                'staff'     =>  ['create' => true, 'read' => true, 'update' => true, 'delete' => true],
                'client'    =>  ['create' => true, 'read' => true, 'update' => true, 'delete' => true],
                'product'   =>  ['create' => true, 'read' => true, 'update' => true, 'delete' => true],
                'invoice'   =>  ['create' => true, 'read' => true, 'update' => true, 'delete' => true],
                'order'     =>  ['create' => true, 'read' => true, 'update' => true, 'delete' => true],
                'note'      =>  ['create' => true, 'read' => true, 'update' => true, 'delete' => true],
                'ticket'    =>  ['create' => true, 'read' => true, 'update' => true, 'delete' => true],
                'report'    =>  ['create' => true, 'read' => true, 'update' => true, 'delete' => true],
                'instance'  =>  ['create' => true, 'read' => true, 'update' => true, 'delete' => true],
                'notice'    =>  ['create' => true, 'read' => true, 'update' => true, 'delete' => true],
                'module'    =>  ['create' => true, 'read' => true, 'update' => true, 'delete' => true],
                'settings'  =>  ['create' => true, 'read' => true, 'update' => true, 'delete' => true],
                'issue'     =>  ['create' => true, 'read' => true, 'update' => true, 'delete' => true],
                'log'       =>  ['create' => true, 'read' => true, 'update' => true, 'delete' => true],
                'security'  =>  ['create' => true, 'read' => true, 'update' => true, 'delete' => true],
            ];
            $role = [
                'role_name' => 'superadmin',
                'permissions' => serialize($permissions)
            ];
            try {
                $m->insert($role);
            } catch (\Throwable $e) {
                throw new MigrationException("Unable to Insert Into staff_roles. {$e->getMessage()}", (int) $e->getCode(), $e);
            }
        });
        return;
    }
}
