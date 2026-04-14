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

/*============================= OPTION HOOKS =============================*/
/**
 * Get App DB Option
 * @param string $key DB Option entity
 * @param mixed $default Option Default Value
 * @return string
 */
add_hook('option', function(string $key, mixed $default = ''){
    return LBM\Support\Option::get($key, $default);
}, 1000);

/**
 * Get App DB Option as Int
 * @param string $key DB Option entity
 * @return int
 */
add_hook('option.int', function(string $key, int $default = 0): int{
    $value = do_hook('option', $key, $default);
    return preg_match('/^[0-9]+$/i', (string) $value) ? (int) $value : $default;
}, 1000);

/**
 * Get App DB Option as Bool
 * @param string $key DB Option entity
 * @return bool
 */
add_hook('option.bool', function(string $key): bool {
    $value = \do_hook('option', $key, 'no');
    return preg_match('/^(yes|enabled|enable|true|on|1)$/i', $value) ? true : false;
}, 1000);

/*============================= APP HOOKS =============================*/
/**
 * Get App Name
 * @return string
 */
add_hook('app.name', function(){
    return \do_hook('option', 'app.name', 'Laika Bill Manager');
}, 1000);

/**
 * App Logo
 * @param ?string $key Option Table lkey column. Example: admin.logo app.logo
 * @return string
 */
add_hook('app.logo', function(?string $key = null): string {
    $name = \do_hook('option', $key ?: 'app.logo', 'logo.png');
    return \named('app.src', ['name'=>"/img/{$name}"], true);
}, 1000);

/**
 * App Logo
 * @param ?string $key Option Table lkey column. Example: admin.logo app.logo
 * @return string
 */
add_hook('app.icon', function(?string $key = null): string {
    $name = \do_hook('option', $key ?: 'app.icon', 'icon.png');
    return \named('app.src', ['name'=>"/img/{$name}"], true);
}, 1000);

/**
 * Panel Info
 * Remove Later
 */
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

/**
 * Make Log URL
 * Remove Later
 */
add_hook('log.url', function (string $label, string $named, array $param = []) {
    return '<a href="' . named($named, $param, true) . '">' . $label . '</a>';
}, 1000);

/*============================= MESSAGE HOOKS =============================*/
/**
 * Create Redirect Message
 */
add_hook('redirect.message', function (string $userMesssage, string $exceptionMessage) {
    return DEBUG ? $exceptionMessage : $userMesssage;
});

/*============================= FORM HOOKS =============================*/
/**
 * Option is Selected
 * @param string $key Request Key Name. Example: 'status'
 * @param ?string $existing Existing Value. Example: 'active'
 * @param ?string $match Match Value (Changable as per loop); Example: 'active'
 * @return string
 */
add_hook('selected', function(string $key, ?string $existing, ?string $match): string {
    return (do_hook('request.input', $key, (string) $existing)) === (string) $match ? 'selected' : '';
}, 1000);

/**
 * Form CSRF Validateion
 * @param string $type CSRF Type ADMIN/CLIENT
 * @param string $redirect Redirect Route Name
 * @return array
 */
add_hook('csrf.validate', function(string $type): array {
    if (!call_user_func([new \Laika\Core\Helper\CSRF($type), 'validate'])) {
        return ['status' => false, 'message' => LANG::$invalidCsrf];
    }
    return ['status' => true, 'message' => LANG::$validCsrf];
}, 1000);

/**
 * Get Form Error
 * @param string $key Form Field Key Name
 * @return string
 */
add_hook('form.error', function(string $key): string {
    $errors = \Laika\Core\Http\FormError::get($key);
    return $errors[0] ?? '';
});

/*============================= HTML HOOKS =============================*/
/**
 * Active Class for Navigation
 * @param string $key Input Key to Get Value
 * @param string $value Input Key Value to Match
 * @return string
 */
add_hook('class.active', function(string $key, string $value = ''): string {
    return do_hook('request.input', $key) === $value ? 'active' : '';
});

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

/*========================== ADMIN FILTERS ==========================*/
/**
 * Check Staff Has Access
 */
add_hook('staff_has_access', 'staff_has_access', 1000);