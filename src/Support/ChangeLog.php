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

class ChangeLog
{
    /**
     * @var array $logs Change logs
     */
    private array $logs;

    private function __construct()
    {
        $this->logs = [];
    }

    /**
     * Validate Change Exists
     * @param mixed $old Old Value
     * @param mixed $new Nwe Value
     * @return bool
     */
    public function isChanged(mixed $old, mixed $new): bool
    {
        return $old === $new;
    }

    /**
     * Add Change Log
     * @param mixed $old Old Value
     * @param mixed $new Nwe Value
     * @return array
     */
    public function add(string $old, string $new): void
    {
        $this->logs[] = ['old' => $old, 'new' => $new];
        return;
    }

    /**
     * Get Logs
     * @return array
     */
    public function logs(): array
    {
        return $this->logs;
    }
}