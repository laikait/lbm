<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

#########################################################################
/*---------------------------- ADMIN HOOKS ----------------------------*/
#########################################################################

declare(strict_types=1);

/*================================ CSRF ================================*/
add_hook('csrf.field.admin', function (): string{
    return do_hook('csrf.field', ADMIN);
}, 1000);

/*========================== TEMPLATE FILTERS ==========================*/
add_hook('admin.template', function(){
    return 'admin/' . do_hook('option', 'admin.template', 'default');
}, 1000);

/*=============================== ACCESS ===============================*/
add_hook('admin.has.access', function(string $access){ // Complete it Later
    list($name,$action) = explode('.', $access);
    return true;
}, 1000);