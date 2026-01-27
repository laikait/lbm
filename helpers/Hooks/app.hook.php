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
 * @param string $key DB Option lkey Name
 * @param mixed $default Option Default Value
 * @return string
 */
add_hook('option', function(string $key, mixed $default = ''){
    return LBM\Factory\Option::get($key, Laika\Core\Helper\Config::get('env', $key, $default));
}, 1000);

/**
 * Get App DB Option as Bool
 * @param string $key DB Option lkey Name
 * @return bool
 */
add_hook('option.bool', function(string $key){
    $value = \do_hook('option', $key, false);
    return \is_bool($value) ? $value : (bool) \preg_match('/^(yes|enable|true|on|1)$/i', $value);
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

/*============================= MESSAGE HOOKS =============================*/
/**
 * Get Notification Message
 */
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

/*============================= FORM HOOKS =============================*/
/**
 * Option is Selected
 * @param string $key Request Key Name. Example: 'status'
 * @param string $value Existing Value. Example: 'active'
 * @param string $match Match Value; Example: 'active'
 * @return string
 */
add_hook('selected', function(string $key, string $value, string $match): string{
    return (do_hook('request.input', $key, $value) == $match) ? 'selected' : '';
}, 1000);

/**
 * Form CSRF Validateion
 * @param string $type CSRF Type ADMIN/CLIENT
 * @param string $redirect Redirect Route Name
 * @return void
 */
add_hook('csrf.validate', function(string $type, string $redirect): void {
    if (!call_user_func([new \Laika\Core\Helper\CSRF($type), 'validate'])) {
        call_user_func([(new \Laika\Core\Http\Redirect()), 'with'], LANG::$invalid_csrf, false)->back($redirect);
    }
}, 1000);

/*============================= HTML HOOKS =============================*/
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