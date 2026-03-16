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
use Laika\Core\Helper\Local;
use Laika\Session\SessionManager;

class Initiate
{
    public function __construct()
    {
        if (!Connection::has()) {
            // Initiate Database
            Connection::add(do_hook('config.database', 'default'));
        }
    }

    // Initiate Defaults
    public function common(?string $user_type = null)
    {
        // Initiate Session
        $dbsession = do_hook('option.bool', 'dbsession');
        $dbsession ? SessionManager::config(Connection::get()) : SessionManager::config();

        // Initiate Local
        switch ($user_type) {
            case ADMIN:
                Local::set(\do_hook('option', 'admin.local', 'en'));
                Local::load('admin');
                break;

            case PANEL:
                Local::set(\do_hook('option', 'panel.local', 'en'));
                Local::load('panel');
                break;
            
            default:
                Local::set(\do_hook('option', 'local', 'en'));
                Local::load();
                break;
        }
    }
}