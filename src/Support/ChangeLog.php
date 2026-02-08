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
    private array $logs = [];

    /**
     * @param array $existing Existing Value
     * @param array $input New Input Value
     */
    public function __construct(array $existing, array $input)
    {
        // Check & Register Changes
        foreach ($input as $key => $new) {
            $old = $existing[$key] ?? '';
            if ($old !== $new) {
                $this->logs[$key] = ['old' => $old, 'new' => $new];
            }
        }
    }

    /**
     * Get Logs
     * @return ?array
     */
    public function logs(): array
    {
        return $this->logs;
    }
}