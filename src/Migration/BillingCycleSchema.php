<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Core\Exceptions\MigrationException;
use App\Model\BillingCycleModel;
use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class BillingCycleSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('billing_cycles', function (Blueprint $t) {
            $t->id('billing_cycle_id');
            $t->string('billing_cycle_name', 50);
            $t->timestamp('billing_cycle_created_at');

            $t->index('billing_cycle_created_at');
        });
    }

    /**
     * Default Values to Insert
     * @return void
     */
    public function default(): void
    {
        $cycles = [
            ['billing_cycle_name' => 'one_time'],
            ['billing_cycle_name' => 'monthly'],
            ['billing_cycle_name' => 'semi_annual'],
            ['billing_cycle_name' => 'annual'],
            ['billing_cycle_name' => 'biennial'],
            ['billing_cycle_name' => 'triennial'],
        ];
        $model = new BillingCycleModel();
        $model->transaction(function (BillingCycleModel $m) use ($cycles) {
            try {
                $m->insert($cycles);
            } catch (\Throwable $e) {
                throw new MigrationException("Unable to Insert Default Billing Cycles. {$e->getMessage()}", (int) $e->getCode(), $e);
            }
        });
        return;
    }
}
