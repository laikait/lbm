<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Core\Exceptions\MigrationException;
use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;
use App\Model\ServerStatus;

class ServerStatusSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('server_statuses', function (Blueprint $t) {
            $t->id('status_id');
            $t->string('status_name', 50)->comment('Status Name');
            $t->string('status_color', 25)->comment('Status Color');
            $t->enum('system_default', ['yes', 'no'])->default('no');

            // Indexes
            $t->unique('status_name');
            $t->index('system_default');
        });
    }

    /**
     * Default Values to Insert
     * @return void
     */
    public function default(): void
    {
        $statuses = [
            ['status_name' => 'online', 'status_color' => '#000000', 'system_default' => 'yes'],
            ['status_name' => 'offline', 'status_color' => '#000000', 'system_default' => 'yes'],
            ['status_name' => 'maintenance', 'status_color' => '#000000', 'system_default' => 'yes']
        ];
        $model = new ServerStatus();
        $model->transaction(function ($m) use ($statuses) {
            try {
                $m->insert($statuses);
            } catch (\Throwable $e) {
                throw new MigrationException("Unable to Insert Default Into 'server_statuses. {$e->getMessage()}", (int) $e->getCode(), $e);
            }
        });
        return;
    }
}
