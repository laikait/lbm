<?php

declare(strict_types=1);

// Namespace
namespace LBM\Migration;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Core\Exceptions\MigrationException;
use App\Model\CountryModel;
use Laika\Model\Schema\Blueprint;
use Laika\Model\Schema\Schema;

class CountrySchema
{
    /**
     * Migrate Table
     */
    public function migrate()
    {
        Schema::on()->createIfNotExists('countries', function (Blueprint $t) {
            $t->id('country_id');
            $t->char('iso2', 2);
            $t->char('iso3', 3);
            $t->string('country_name', 100);
            $t->string('phone_code', 5);

            $t->unique('iso2');
            $t->unique('iso3');
        });
    }

    /**
     * Default Values to Insert
     * @return void
     */
    public function default(): void
    {
        $model = new CountryModel();
        $model->transaction(function (CountryModel $m) {
            $json = file_get_contents(APP_PATH . '/lf-config/json/countries.json');
            try {
                $m->insert(json_decode($json, true));
            } catch (\Throwable $e) {
                throw new MigrationException("Unable to Insert Into countries. {$e->getMessage()}", (int) $e->getCode(), $e);
            }
        });
        return;
    }
}
