<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Core\Exceptions\MigrationException;
use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;
use App\Model\InvoiceStatusModel;

class InvoiceStatusSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('invoice_statuses', function (Blueprint $t) {
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
            ['status_name' => 'unpaid', 'status_color' => '#000000', 'system_default' => 'yes'],
            ['status_name' => 'paid', 'status_color' => '#000000', 'system_default' => 'yes'],
            ['status_name' => 'draft', 'status_color' => '#000000', 'system_default' => 'yes'],
            ['status_name' => 'overdue', 'status_color' => '#000000', 'system_default' => 'yes'],
            ['status_name' => 'cancelled', 'status_color' => '#000000', 'system_default' => 'yes'],
            ['status_name' => 'refunded', 'status_color' => '#000000', 'system_default' => 'yes']
        ];
        $model = new InvoiceStatusModel();
        $model->transaction(function (InvoiceStatusModel $m) use ($statuses) {
            try {
                $m->insert($statuses);
            } catch (\Throwable $e) {
                throw new MigrationException("Unable to Insert Default Into invoice_statuses. {$e->getMessage()}", (int) $e->getCode(), $e);
            }
        });
        return;
    }
}
