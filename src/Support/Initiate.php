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

use Laika\Core\Service\Redirect;
use Laika\Core\Service\Request;
use Laika\Core\Service\Local;
use Laika\Core\Service\Date;
use Laika\Core\Service\Csrf;
use Laika\Session\Relay\Session;
use Laika\Model\Connection;
use LANG;

class Initiate
{
    public function __construct()
    {
        // Initiate Database
        if (!Connection::has()) Connection::add(config('database', 'default'));

        // Default Timezone & Format
        Date::setFormat(option('datetime_format', 'Y-m-d H:i:s'))->setAppTimezone(option('time_zone', 'UTC'));

        // Apply Database Session Timezone
        Connection::applyTimezone(Date::getOffset());
    }

    /**
     * Initiate Defaults
     * @param ?string $type
     * @return void
     */
    public function common(?string $type = null): void
    {
        // Initiate Session
        Session::config(Connection::get());

        Session::for($type);

        // Initiate Local
        switch (strtolower((string) $type)) {
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