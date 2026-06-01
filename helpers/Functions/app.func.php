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
use Laika\Core\Service\Url;
use Laika\Core\Service\Date;
use Laika\Core\Service\Math;
use LBM\Service\Currency;
use LBM\Service\PasswordValidator;

/**
 * Get App DB Option
 * @param string $key DB Option entity
 * @param mixed $default Option Default Value
 * @return string
 */
function option(string $key, mixed $default = '')
{
    static $options = [];
    if (!array_key_exists($key, $options)) {
        $options[$key] = Option::get($key, $default);
    }
    return $options[$key];
};

/**
 * Get App DB Option as Int
 * @param string $key DB Option entity
 * @return int
 */
function option_int(string $key, int $default = 0): int
{
    $value = option($key, $default);
    return preg_match('/^[0-9]+$/i', (string) $value) ? (int) $value : $default;
};

/**
 * Get App DB Option as Bool
 * @param string $key DB Option entity
 * @param bool $default Default
 * @return int
 */
function option_bool(string $key, bool $default = false): bool
{
    $value = option($key, 'no');
    return preg_match('/^(yes|enabled|enable|true|on|1)$/i', $value) ? true : $default;
};

/**
 * Get Default Currency
 * @return array
 */
function default_currency(): array
{
    static $currency = null;
    if ($currency === null) $currency = Currency::default();
    return $currency;
}

/**
 * Decimal Symbol
 * @return string
 */
function decimal_symbol(): string
{
    static $symbol = null;
    if ($symbol === null) $symbol = option('decimal_symbol', '.');
    return $symbol;
}

/**
 * Thousand Separator
 * @return string
 */
function thousand_separator(): string
{
    static $separator = null;
    if ($separator === null) $separator = option('thousand_separator', '.');
    return $separator;
}

/**
 * Decimal Format
 * @param string|float|int $amount
 * @return string
 */
function decimal(string|float|int $amount): string
{
    $amount = preg_replace('/[^0-9.\-]+/i', '', (string) $amount);
    return number_format((float) $amount, 2, decimal_symbol(), thousand_separator());
};

/**
 * Format Currency
 * @param string|float|int $amount
 * @return string
 */
function format_currency(string|float|int $amount): string
{
    $amount = preg_replace('/[^0-9.\-]+/i', '', (string) $amount);
    $currency = default_currency();
    return (string) $currency['prefix_symbol'] . number_format((float) $amount, 2, decimal_symbol(), thousand_separator()) . (string) $currency['suffix_symbol'];
};

/**
 * To App Date Format
 * @param null|string $time
 * @return string
 */
function format_date(null|string $time): string
{
    return $time ? Date::parse($time)->format() : '';
};

/**
 * Admin template Name
 * @return string
 * */
function admin_template(): string
{
    return 'admin/' . option('admin_template', 'default');
};

/**
 * App Logo
 * @param ?string $key Option Table lkey column. Example: admin.logo app.logo
 * @return string
 */
function app_icon(?string $key = null): string
{
    $name = option($key ?: 'app_icon', 'icon.png');
    return named('app.src', ['path' => "/img/{$name}"]);
};

/**
 * App Logo
 * @param ?string $key Option Table lkey column. Example: admin.logo app.logo
 * @return string
 */
function app_logo(?string $key = null): string
{
    $name = option($key ?: 'app_logo', 'logo.png');
    return named('app.src', ['name' => "/img/{$name}"]);
};

/**
 * Get App Host
 * @return string
 */
function app_uri()
{
    return option('app_host', Url::base());
};

/**
 * Get App Name
 * @return string
 */
function app_name()
{
    return option('app_name', 'Laika Bill Manager');
};

/**
 * Get Data Limit
 * @param ?int $default = null
 * @return int
 */
function data_limit(?int $default = null): int
{
    static $limit = null;
    $default = $default && ($default > 0) ? $default : 20;
    if ($limit === null) $limit = option_int('data_limit', $default ?? 20);
    return $limit;
}

/**
 * Get Total Pages
 * @param int|string $totalRows
 * @return int
 */
function total_pages(int|string $totalRows): int
{
    $totalRows = (int) $totalRows;
    return $totalRows > data_limit() ? ceil($totalRows / data_limit()) : 1;
}

/**
 * Validate Password
 * @param string $password
 * @return bool
 */
function validate_password(string $password): bool
{
    $res = PasswordValidator::validate($password);
    return $res['status'];
}

/**
 * Make Return
 * @param bool $status
 * @param string $message
 * @param array $data
 * @return array
 */
function make_return(bool $status, string $message, array $data = []): array
{
    return [
        'status' => $status,
        'message' => $message,
        'data' => $data
    ];
}
