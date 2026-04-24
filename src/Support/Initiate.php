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

use Laika\Model\Connection;
use Laika\Core\Relay\Relays\Local;
use Laika\Core\Relay\Relays\Date;
use Laika\Session\Relay\Session;

class Initiate
{
    public function __construct()
    {
        if (!Connection::has()) {
            // Initiate Database
            Connection::add(do_hook('config.database', 'default'));
        }
        // Date Default Setup
        Date::setTimezone(do_hook('option', 'time.zone', 'UTC'))
            ->setFormat(do_hook('option', 'datetime.format', 'Y-m-d H:i:s'));
    }

    /**
     * Initiate Defaults
     * @param ?string $type
     * @return void
     */
    public function common(?string $type = null): void
    {
        // Initiate Session
        $dbsession = do_hook('option.bool', 'dbsession');
        $dbsession ? Session::config(Connection::get()) : Session::config();
        Session::for($type);

        // Initiate Local
        switch (strtolower((string) $type)) {
            case strtolower(ADMIN):
                Local::set(do_hook('option', 'admin.local', 'en'));
                Local::setPath('admin');
                Local::load();
                break;

            case strtolower(PANEL):
                Local::set(do_hook('option', 'panel.local', 'en'));
                Local::setPath('panel');
                Local::load();
                break;
            
            default:
                Local::load();
                break;
        }
    }
}