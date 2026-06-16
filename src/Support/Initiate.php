<?php
/**
 * Laika Bill Master
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This Project Don't Provide Any Permission to Use it In Any Other Webapplication
 */

declare(strict_types=1);

namespace LBM\Support;

use Laika\Core\Service\{Redirect, Request, Local, Date, Csrf, DB, Url};
use Laika\Session\Service\Session;
use Laika\Model\Connection;
use LANG;

class Initiate
{
    /** @var string $local */
    private string $local;

    public function __construct()
    {
        // Initiate Database
        DB::run();

        // Default Timezone & Format
        Date::setFormat(option('datetime_format', 'Y-m-d H:i:s'))->setAppTimezone(option('time_zone', 'UTC'));

        // Apply Database Session Timezone
        Connection::applyTimezone(Date::getOffset());
    }

    /**
     * Initiate Defaults
     * @return void
     */
    public function common(): void
    {
        // Initiate Session
        DB::session();

        // Initiate Local
        switch (strtolower((string) Url::segment(1))) {
            case strtolower(ADMIN):
                Local::set(option('admin_local', 'en'));
                Local::setPath('admin');
                Local::load();
                break;

            case strtolower(PANEL):
                Local::set(option('panel_local', 'en'));
                Local::setPath('panel');
                Local::load();
                break;
            
            default:
                Local::load();
                break;
        }

        // Validate CSRF
        if(Request::isPost() && !Csrf::is_valid()) {
            Redirect::with(LANG::$invalidCsrf, false)->back();
        }
    }
}