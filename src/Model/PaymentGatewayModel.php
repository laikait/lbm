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

namespace App\Model;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\Model\Model;

class PaymentGatewayModel extends Model
{
    // Table Name
    protected string $table = 'payment_gateways';

    // Primary Column Name
    protected string $id = 'gateway_id';

    // Cast Columns
    protected array $casts = ['settings' => 'serialize', 'supports' => 'json']; // Example: ['col_1' => 'int', 'col_2' => 'string', ....]

    // Start Code From Here
}
