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

namespace LBM\Support;

use Laika\Core\Service\Regex;
use LANG;

class PasswordValidator
{
    /**
     * Validate Password
     * @param string $input
     * @return array{message: string, status: bool}
     */
    public function validate(string $input): array
    {
        $min_length = option_int('require_minimum_length', 6);
        $require_upper = option_bool('require_upper_case', true);
        $require_lower = option_bool('require_lower_case', true);
        $require_special = option_bool('require_special_char', true);
        $require_numeric = option_bool('require_numeric', true);

        // Check Minimum Length
        if ((strlen($input) < $min_length)) return ['message' => sprintf(LANG::$minLength, $min_length), 'status' => false];

        // Check Upper Exists
        if ($require_upper) {
            if (!Regex::validate('hasupper', $input)) {
                return ['message' => LANG::$upperCharRequired, 'status' => false];
            }
        }
        // Check Lower Exists
        if ($require_lower) {
            if (!Regex::validate('haslower', $input)) {
                return ['message' => LANG::$lowerCharRequired, 'status' => false];
            }
        }
        // Check Special Exists
        if ($require_special) {
            if (!Regex::validate('hasspecial', $input)) {
                return ['message' => LANG::$specialCharRequired, 'status' => false];
            }
        }
        // Check Numeric Exists
        if ($require_numeric) {
            if (!Regex::validate('hasspecial', $input)) {
                return ['message' => LANG::$numericRequired, 'status' => false];
            }
        }
        return ['message' => 'Successfully Validated', 'status' => true];
    }
}