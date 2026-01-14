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

class Form
{
    /**
     * @var Form $form Form Static Object
     */
    private static Form $form;

    /**
     * @var array $errors Form Errors
     */
    private array $errors;

    private function __construct()
    {
        $this->errors = [];
    }

    /**
     * Initiate Form Object
     * @return Form
     */
    private static function instance(): Form
    {
        self::$form ??= new self();
        return self::$form;
    }

    /**
     * Add Form Error
     * @param string $name Addons Name. Example: 'domain'
     * @param array $value Addons Value. Example: ['title' => '<any_title>','url'=>'<any_link>']
     * @return void
     */
    public static function add(string $name, array $value): void
    {
        self::instance()->errors[$name] = $value;
        return;
    }

    public static function get(?string $name = null): array
    {
        return empty($name) ? self::instance()->errors : self::instance()->errors[$name];
    }
}