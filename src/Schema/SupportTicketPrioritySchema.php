<?php

declare(strict_types=1);

// Namespace
namespace LBM\Schema;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Core\Exceptions\SchemaException;
use LBM\Model\SupportTicketPriorityModel;
use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class SupportTicketPrioritySchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('support_priorities', function (Blueprint $t) {
            $t->id('priority_id');
            $t->string('priority_name');
            $t->string('priority_color');

            // Indexes
            $t->unique('priority_name');
        });
    }

    /**
     * Default Values to Insert
     * @return void
     */
    public function default(): void
    {
        $model = new SupportTicketPriorityModel();
        $model->transaction(function (SupportTicketPriorityModel $m) {
            try {
                $default = [
                    ['priority_name' => 'low', 'priority_color' => '#000000'],
                    ['priority_name' => 'medium', 'priority_color' => '#000000'],
                    ['priority_name' => 'high', 'priority_color' => '#000000'],
                    ['priority_name' => 'urgent', 'priority_color' => '#000000']
                ];
                $m->insert($default);
            } catch (\Throwable $e) {
                throw new SchemaException("Unable to Insert Into 'support_priorities'. {$e->getMessage()}", (int) $e->getCode(), $e);
            }
        });
        return;
    }
}
