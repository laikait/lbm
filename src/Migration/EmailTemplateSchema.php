<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Core\Exceptions\MigrationException;
use App\Model\EmailTemplateModel;
use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class EmailTemplateSchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('email_templates', function (Blueprint $t) {
            $t->id('et_id');
            $t->string('slug')->comment('Example: client-welcome, invoice-created');
            $t->string('name', 100)->comment('Example: client_welcome, invoice_created');
            $t->string('subject');
            $t->longText('body');
            $t->json('variables')->comment('Documentation of available {variables}');
            $t->enum('is_active', ['yes', 'no'])->default('yes');
            $t->timestamps();

            // Indexes
            $t->unique('slug');
            $t->unique('name');
            $t->index('is_active');
        });
    }

    /**
     * Default Values to Insert
     * @return void
     */
    public function default(): void
    {
        $model = new EmailTemplateModel();
        $model->transaction(function (EmailTemplateModel $m) {
            try {
                $m->insert([
                    "slug" => "new-client",
                    "name" => "client_welcome",
                    "subject" => "Welcome To {company_name}",
                    "body" => htmlspecialchars("<p>Hello {client_name}</p><p>Welcome to {company_name}. Your Registration is successful. Your Credentials:</p><p>Email: {client_email}</p><p>Username: {client_username}</p><p>Password: ********* (Protected)</p>"),
                    "variables" => json_encode(['client_name', 'company_name', 'client_email', 'client_username'], JSON_PRETTY_PRINT)
                ]);
            } catch (\Throwable $e) {
                throw new MigrationException("Unable to Insert Into clients. {$e->getMessage()}", (int) $e->getCode(), $e);
            }
        });
        return;
    }
}
