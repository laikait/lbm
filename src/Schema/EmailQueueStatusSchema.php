<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Core\Exceptions\SchemaException;
use LBM\Model\EmailQueueStatusModel;
use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class EmailQueueStatusSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('email_queue_statuses', function (Blueprint $t) {
            $t->id('status_id')->comment('Status ID');
            $t->enum('status_name', ['queued', 'completed', 'failed', 'manual'])->default('queued')->comment('Status Name');
            $t->string('status_color', 25)->comment('Status Color');

            $t->index('status_name');
        });
    }

    /**
     * Default Values to Insert
     * @return void
     */
    public function default(): void
    {
        $model = new EmailQueueStatusModel();
        $model->transaction(function (EmailQueueStatusModel $m) {
            try {
                $default = [
                    ['status_name' => 'queued', 'status_color' => '#000000'], // Modify Later
                    ['status_name' => 'completed', 'status_color' => '#000000'],
                    ['status_name' => 'failed', 'status_color' => '#000000'],
                    ['status_name' => 'manual', 'status_color' => '#000000'],
                ];
                $m->insert($default);
            } catch (\Throwable $e) {
                throw new SchemaException("Unable to Insert Into 'email_queue_statuses'. {$e->getMessage()}", (int) $e->getCode(), $e);
            }
        });
        return;
    }
}
