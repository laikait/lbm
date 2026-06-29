<?php
/**
 * Laika Framework
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This file is part of the Laika Framework.
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace LBM\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Model;

class CurrencyModel extends Model
{
    // Table Name
    protected string $table = 'currencies';

    // Primary Column Name
    protected string $id = 'currency_id';

    // Cast Columns
    protected array $casts = []; // Example: ['col_1' => 'int', 'col_2' => 'string', ....]

    /**
     * Get Default Currency
     * @return array
     */
    public function default(): array
    {
        return $this->where(['is_default' => 'yes'])->first();
    }
}
