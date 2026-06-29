<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Core\Exceptions\SchemaException;
use LBM\Model\InvoiceItemTypeModel;
use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class InvoiceItemTypeSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('invoice_item_types', function (Blueprint $t) {
            $t->id('type_id');
            $t->string('type_name', 50)->comment('Item Type Name');
            $t->timestamp('type_created_at');

            $t->unique('type_name');
            $t->index('type_created_at');
        });
    }

    /**
     * Default Values to Insert
     * @return void
     */
    public function default(): void
    {
        $model = new InvoiceItemTypeModel();
        $model->transaction(function ($m) {
            try {
                $default = [
                    ['type_name' => 'product'],
                    ['type_name' => 'addon'],
                    ['type_name' => 'domain'],
                    ['type_name' => 'setup'],
                    ['type_name' => 'usage'],
                    ['type_name' => 'credit'],
                    ['type_name' => 'discount'],
                    ['type_name' => 'other']
                ];
                $m->insert($default);
            } catch (\Throwable $e) {
                throw new SchemaException("Unable to Insert Into 'invoice_item_types'. {$e->getMessage()}", (int) $e->getCode(), $e);
            }
        });
        return;
    }
}
