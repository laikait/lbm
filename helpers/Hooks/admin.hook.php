<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

/*============================= ADMIN HOOKS =============================*/

declare(strict_types=1);

/*================================ CSRF ================================*/
/**
 * CSRF Field
 */
add_hook('csrf.field.admin', function (): string{
    return do_hook('csrf.field', ADMIN);
}, 1000);

/*========================== TEMPLATE FILTERS ==========================*/
/**
 * Admin template Name
 */
add_hook('admin.template', function(){
    return 'admin/' . do_hook('option', 'admin.template', 'default');
}, 1000);
