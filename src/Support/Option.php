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

use Laika\App\Model\OptionsModel;

class Option
{
    /**
     * Get Option Value
     * @param string $key - Required Argument as Option Key.
     * @param mixed $value - If No Valu Exists/Found, Default will Return.
     * @return mixed
     */
    public static function get(string $key, mixed $value = null): mixed
    {
        try {
            $model = new OptionsModel();
            $option = $model->where(['key' => $key])->first();
            $value = $option['value'] ?? $value;
        } catch (\Throwable $th) {}
        return $value;
    }

    /**
     * Set Option
     * @param string $key Required Argument. Option Name
     * @param string $value Required Argument. Option Value
     * @param bool $default Optional Argument. Default is false
     */
    public static function set(string $key, string $value, bool $default = false): bool
    {
        $model = new OptionsModel();
        $default = $default ? 'yes' : 'no';

        // Check Option Name Doesn't Exists
        if (empty($model->first(['key' => $key]))) {
            return (bool) $model->insert([
                'key' => $key,
                'value' => $value,
                'is_default' => $default
            ]);
        }

        // Update Value
        return (bool) $model->update(['key' => $key], ['value' => $value]);
    }
}
