<?php

/**
 * Cloud Bill Master
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This Project Don't Provide Any Permission to Use it In Any Other Webapplication
 */

declare(strict_types=1);

// Namespace
namespace LBM\Factory;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403) . die('403 Direct Access Denied!');

class Addons
{
    /**
     * @var Addons $addons Addonse Static Object
     */
    private static Addons $addons;

    /**
     * @var array $lists Listed Addons
     */
    private array $lists;

    private function __construct()
    {
        $this->lists = [];
    }

    /**
     * Initiate Addons Object
     * @return Addons
     */
    private static function instance(): Addons
    {
        self::$addons ??= new self();
        return self::$addons;
    }

    /**
     * Add Addon
     * @param string $name Addons Name. Example: 'domain'
     * @param array $value Addons Value. Example: ['title' => '<any_title>','url'=>'<any_link>']
     * @return void
     */
    public static function add(string $name, array $value): void
    {
        self::instance()->lists[$name] = $value;
        return;
    }

    public static function get(?string $name = null): array
    {
        return empty($name) ? self::instance()->lists : self::instance()->lists[$name];
    }
}