<?php
/**
 * Laika Bill Master
 * Author: Showket Ahmed
 * Email: riyadhtayf@gmail.com
 * License: MIT
 * This Project Don't Provide Any Permission to Use it In Any Other Webapplication
 */

declare(strict_types=1);

namespace LBM\Support;

use LBM\Action\AuthUser;
use Laika\Core\Relay\Relays\Request;
use Laika\Core\Relay\Relays\Response;
use LBM\Exception\SupportException;

class Authentication
{
    /** @var string $type */
    protected string $type;

    public function __construct(string $type)
    {
        if (!in_array($type, [ADMIN, PANEL])) {
            throw new SupportException("Invalid User Type: [{$type}]");
        }
        $this->type = $type;
    }

    /**
     * Make Authentication
     * @return ?array
     */
    public function login(): ?array
    {
        $auth = new AuthUser();
        return match ($this->type) {
            ADMIN   =>  $auth->staff_login(),
            PANEL   =>  $auth->panel_login(),
            default =>  null
        };
    }

    /**
     * Check Authentication
     * @return ?array
     */
    public function validate(): ?array
    {
        $auth = new AuthUser($this->request, $this->response);
        return match ($this->type) {
            ADMIN   =>  $auth->validateStaffLogin(),
            PANEL   =>  $auth->validatePanelLogin(),
            default =>  null
        };
    }

}