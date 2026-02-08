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
namespace LBM\Support;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403) . die('403 Direct Access Denied!');

class FormError
{
    /**
     * @var FormError $formError Form Static Object
     */
    private static FormError $formError;

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
     * @return FormError
     */
    private static function instance(): FormError
    {
        self::$formError ??= new self();
        return self::$formError;
    }

    /**
     * Add Form Error
     * @param array $errors Form Errors
     * @return void
     */
    public static function add(array $errors): void
    {
        self::instance()->errors = $errors;
        return;
    }

    /**
     * Check Form Has Error
     * @return bool
     */
    public static function hasError(): bool
    {
        return !empty(self::instance()->errors);
    }

    /**
     * Get Form Errors
     * @param string|null $key Get Specific Key Errors
     * @return array
     */
    public static function get(?string $key = null): array
    {
        return empty($key) ? self::instance()->errors : self::instance()->errors[$key] ?? [];
    }
}