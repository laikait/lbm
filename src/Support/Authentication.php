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
use Laika\Core\Http\Request;
use Laika\Core\Http\Response;
use LBM\Exception\SupportException;

class Authentication
{
    /** @param Request $request */
    protected Request $request;

    /** @param Response $response */
    protected Response $response;

    /** @param string $type */
    protected string $type;

    public function __construct(string $type, ?Request $request = null, ?Response $response = null)
    {
        if (!in_array($type, [ADMIN, PANEL])) {
            throw new SupportException("Invalid User Type: [{$type}]");
        }
        $this->type = $type;
        $this->request = empty($request) ? new Request() : $request;
        $this->response = empty($response) ? new Response() : $response;
    }

    /**
     * Make Authentication
     * @return ?array
     */
    public function login(): ?array
    {
        $auth = new AuthUser($this->request, $this->response);
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