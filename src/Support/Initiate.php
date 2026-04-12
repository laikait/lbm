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
use Laika\Session\SessionManager;

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
            ->setFormat(do_hook('option', 'datetime.format', 'Y-m-d H:i:s'))
            ->init();
    }

    /**
     * Initiate Defaults
     * @param ?string $user_type
     * @return void
     */
    public function common(?string $user_type = null): void
    {
        // Initiate Session
        $dbsession = do_hook('option.bool', 'dbsession');
        $dbsession ? SessionManager::config(Connection::get()) : SessionManager::config();

        // Initiate Local
        switch (strtolower((string) $user_type)) {
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