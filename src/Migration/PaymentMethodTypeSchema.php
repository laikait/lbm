<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Core\Exceptions\MigrationException;
use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;
use App\Model\PaymentMethodTypeModel;

class PaymentMethodTypeSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('payment_method_types', function (Blueprint $t) {
            $t->id('pm_type_id');
            $t->string('type_name', 100);
            $t->enum('is_default', ['yes', 'no'])->default('no');

            // Indexes
            $t->unique('type_name');
            $t->index('is_default');
        });
    }

    /**
     * Default Values to Insert
     * @return void
     */
    public function default(): void
    {
        $model = new PaymentMethodTypeModel();
        $model->transaction(function ($m) {
            try {
                $default = [
                    ['type_name' => 'card', 'is_default' => 'yes'],
                    ['type_name' => 'bank_account', 'is_default' => 'yes'],
                    ['type_name' => 'paypal', 'is_default' => 'yes'],
                    ['type_name' => 'other', 'is_default' => 'yes']
                ];
                $m->insert($default);
            } catch (\Throwable $e) {
                throw new MigrationException("Unable to Insert Into 'payment_method_types' {$e->getMessage()}", (int) $e->getCode(), $e);
            }
        });
        return;
    }
}
