<?php

/**
 * Laika Bill Manager
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 */

declare(strict_types=1);

/*============================= ADMIN HOOKS =============================*/
// /**
//  * Match Database Columns with Queries
//  * @return array
//  */
// add_hook('accepted.queries', function (array $acceptedQueries) {
//     $acceptedQueries = array_values($acceptedQueries);
//     $queries = [];
//     $inputs = \do_hook('request.inputs');
//     // Get Accepted Query Values
//     foreach($inputs as $k => $v) {
//         if (in_array($k, $acceptedQueries)) {
//             $queries[$k] = $v;
//         }
//     }
//     return $queries;
// });

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
