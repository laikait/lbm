<?php
/**
 * Laika PHP MVC Framework
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This file is part of the Laika PHP MVC Framework.
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Core\App\Router;
use Laika\Core\Api\Api;

/**
 * {version} Example = example.com/admin/api/v1/login
 */
Router::group(ADMIN.'/api', function(){
    // Get Clients
    Router::get('/{version}/clients', 'Api\Staff\ClientController@clients')->name('staff.api.clients');


    // Set Fallback
    Router::fallback(function(){
        $api = new Api();
        $api->message("Invali Url Detected!");
        $api->send([], 404);
        return;
    }, ADMIN.'/api');
});