<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

##########################################################################
/*---------------------------- COMMON HOOKS ----------------------------*/
##########################################################################

declare(strict_types=1);

// Panel Info
add_hook('panel', function(){
    $arr = [
        'admin' =>  [
            'url'  =>  do_hook('app.host') . ADMIN
        ],
        'client' =>  [
            'url'  =>  do_hook('app.host') . PANEL
        ],
        'front' =>  [
            'url'  =>  do_hook('app.host')
        ],
    ];
    return $arr;
}, 1000);

// Data Limit
add_hook('data.limit', function(): int{
    return do_hook('option', 'data.limit', 20);
}, 1000);

// Get Notification Message
add_hook('message.get', function(): string {
    $m = do_hook('message.show');
    if(!$m) {
        return '';
    }
    // Return Success Message
    if ($m['status']) {
        return "<div id=\"app-success-message\">{$m['info']}</div>";
    }
    return "<div id=\"app-error-message\">{$m['info']}</div>";
}, 1000);