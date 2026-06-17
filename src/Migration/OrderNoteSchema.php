<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Core\Exceptions\MigrationException;
use App\Model\OrderNoteModel;
use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class OrderNoteSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('order_notes', function (Blueprint $t) {
            $t->bigId('note_id');
            $t->unsignedBigInteger('order_relid')->comment('orders -> oid');
            $t->enum('creator_type', ['client', 'staff', 'system']);
            $t->unsignedBigInteger('creator_relid')->nullable()->default(null);
            $t->text('note');
            $t->timestamp('note_created_at');

            // Indexes
            $t->index('order_relid');
            $t->index(['creator_type', 'creator_relid'], 'note_created_by');
            $t->index('note_created_at');
        });
    }

    /**
     * REMOVE
     * Default Values to Insert
     * @return void
     */
    public function default(): void
    {
        $model = new OrderNoteModel();
        $model->transaction(function (OrderNoteModel $m) {
            try {
                $m->insert([
                    'order_relid' => 1,
                    'creator_type' => 'client',
                    'creator_relid' => 1,
                    'note' => 'New Order Created By Client ID 1'
                ]);
                $m->insert([
                    'order_relid' => 1,
                    'creator_type' => 'client',
                    'creator_relid' => 1,
                    'note' => 'New Order Status Pending By Client ID 1'
                ]);
            } catch (\Throwable $e) {
                throw new MigrationException("Unable to Insert Into order_notes. {$e->getMessage()}", (int) $e->getCode(), $e);
            }
        });
        return;
    }
}
