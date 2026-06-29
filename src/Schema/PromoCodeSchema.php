<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class PromoCodeSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('promo_codes', function (Blueprint $t) {
            $t->id('promo_id');
            $t->string('promo_code', 100);
            $t->enum('promo_type', ['percentage','fixed'])->default('percentage');
            $t->decimal('promo_value', 18, 4);
            $t->unsignedInteger('currency_relid')->nullable()->comment('for fixed type only');
            $t->unsignedInteger('max_uses')->nullable()->comment('NULL = unlimited');
            $t->unsignedInteger('used_count')->default(0);
            $t->enum('applies_to', ['all','products','domains'])->default('all');
            $t->json('product_ids')->nullable()->comment('if applies_to = products');
            $t->timestamp('start_date');
            $t->timestamp('end_date')->nullable()->default(null);
            $t->enum('is_recurring', ['yes', 'no'])->default('no');
            $t->enum('is_active', ['yes', 'no'])->default('no');
            $t->timestamps('promo_created_at', 'promo_updated_at');

            // Indexes
            $t->unique('promo_code');
            $t->index('currency_relid');
            $t->index('promo_created_at');
            $t->index('promo_updated_at');
        });
    }
}
