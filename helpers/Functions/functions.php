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

use LBM\Support\Option;
use Laika\Core\Service\Date;

/**
 * Get App DB Option
 * @param string $key DB Option entity
 * @param mixed $default Option Default Value
 * @return string
 */
function option(string $key, mixed $default = ''){
    return Option::get($key, $default);
};

/**
 * Get App DB Option as Int
 * @param string $key DB Option entity
 * @return int
 */
function option_int(string $key, int $default = 0): int{
    $value = option($key, $default);
    return preg_match('/^[0-9]+$/i', (string) $value) ? (int) $value : $default;
};

/**
 * Get App DB Option as Bool
 * @param string $key DB Option entity
 * @return int
 */
function option_bool(string $key): bool{
    $value = option($key, 'no');
    return preg_match('/^(yes|enabled|enable|true|on|1)$/i', $value) ? true : false;
};

/**
 * Decimal Format
 * @param string $symbol
 * @param string|float $amount
 * @return string
 */
function decimal(string $symbol, string|float $amount): string {
    return $symbol . number_format((float) $amount, 2, option('decimal.symbol', '.'), option('thousand.separator', ','));
};

/**
 * To App Date Format
 * @param string $time
 * @return string
 */
function date_format(string $time): string {
    return Date::parse($time)->format();
};

/**
 * Admin template Name
 * @return string
 * */
function admin_template(): string {
    return 'admin/' . Option::get('admin.template', 'default');
};

/**
 * App Logo
 * @param ?string $key Option Table lkey column. Example: admin.logo app.logo
 * @return string
 */
function app_icon(?string $key = null): string {
    $name = Option::get($key ?: 'app.icon', 'icon.png');
    return named('asset.src', ['name'=>"/img/{$name}"], true);
};

/**
 * App Logo
 * @param ?string $key Option Table lkey column. Example: admin.logo app.logo
 * @return string
 */
function app_logo(?string $key = null): string {
    $name = option($key ?: 'app.logo', 'logo.png');
    return named('app.src', ['name'=>"/img/{$name}"], true);
};

/**
 * Get App Name
 * @return string
 */
function app_name(){
    return option('app.name', 'Laika Bill Manager');
};
