<?php

/**
 * Cloud Bill Master
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This Project Don't Provide Any Permission to Use it In Any Other Webapplication
 */

declare(strict_types=1);

namespace LBM\Exception;

use Exception;

class ActionException extends Exception
{
    public function __construct(string $message, int $code = 0, \Throwable|null $e = null)
    {
        parent::__construct($message, $code, $e);
    }
}