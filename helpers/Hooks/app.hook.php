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
 * @param string $entity DB Option entity
 * @param mixed $default Option Default Value
 * @return string
 */
add_hook('option', function(string $entity, mixed $default = ''){
    return LBM\Support\Option::get($entity, Laika\Core\Helper\Config::get('env', $entity, $default));
}, 1000);

/**
 * Get App DB Option as Int
 * @param string $entity DB Option entity
 * @return int
 */
add_hook('option.int', function(string $entity, int $default = 0): int{
    return (int) \do_hook('option', $entity, $default);
}, 1000);

/**
 * Get App DB Option as Bool
 * @param string $entity DB Option entity
 * @return bool
 */
add_hook('option.bool', function(string $entity){
    $value = \do_hook('option', $entity, false);
    return \is_bool($value) ? $value : (bool) \preg_match('/^(yes|enabled|enable|true|on|1)$/i', $value);
}, 1000);

/*============================= APP HOOKS =============================*/
/**
 * Get App Name
 * @return string
 */
add_hook('app.name', function(){
    return \do_hook('option', 'app.name', \do_hook('config.app', 'name', 'Laika Billing Application'));
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
 */
add_hook('log.url', function (string $label, string $named, array $param = []) {
    return '<a href="' . named($named, $param, true) . '">' . $label . '</a>';
}, 1000);

/*============================= MESSAGE HOOKS =============================*/
/**
 * Create Redirect Message
 */
add_hook('redirect.message', function (string $userMesssage, string $exceptionMessage) {
    return \do_hook('config.env', 'debug', false) ? $exceptionMessage : $userMesssage;
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
/**
 * App Copyright Text
 * @param string $class CSS Class for Anchor Tag
 * @return string
 */
add_hook('app.copyright', function(string $class = 'app-text-secondary'): string {
    $year = date('Y');
    $name = do_hook('app.name');
    return "&copy; {$year}. <a class=\"{$class}\" href=\"" . named('/', [], true) . "\">{$name}</a> All Rights Reserved.";
});

/**
 * App Powered By Text
 * @param string $class CSS Class for Anchor Tag
 * @return string
 */
add_hook('app.poweredby', function(string $class = 'app-text-secondary'): string {
    return do_hook('option.bool', 'poweredby') ? "Powered By <a class=\"{$class}\" target=\"_blank\" href=\"https://laikait.com\">Laika IT</a>" : '';
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