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
namespace LBM\Abstract;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403) . die('403 Direct Access Denied!');

use Laika\Model\Model;
use Laika\Core\Http\Request;
use Laika\Core\Http\Redirect;
use LBM\Exception\FactoryException;

abstract class Factory
{
    /**
     * @var Model $model
     */
    protected Model $model;

    /**
     * Total Pages
     * @var int $total
     */
    protected int $total;

    /**
     * Page Number
     * @var int $page
     */
    protected int $page;

    /**
     * Data Limit
     * @var int $limit
     */
    protected int $limit;

    /**
     * Accepted Queries
     * @var array
     */
    protected array $acceptedQueries;

    /**
     * Request
     * @var Request $request
     */
    protected Request $request;

    /**
     * Request
     * @var Redirect $request
     */
    protected Redirect $redirect;

    /**
     * Initiate Client Factory
     */
    public function __construct(string $model, array $acceptedQueries = [])
    {
        $this->redirect = new Redirect();
        $this->request = new Request();
        $this->page = (int) \call_user_func([$this->request, 'input'], 'page', 1);
        $this->limit = \do_hook('option.int', 'data.limit', 20);
        $this->acceptedQueries = $acceptedQueries;
        $model = "\\Laika\\App\\Model\\{$model}";
        // Check Model Exists
        if (!class_exists($model)) {
            throw new FactoryException("Invalid Model [{$model}] Provided In Factory");
        }
        $this->model = new $model();
    }

    /**
     * Update Rows by Request
     * @return ?array
     */
    abstract public function create(): ?array;

    /**
     * Update Rows by Request
     * @param array $existing Existing Data to Compare With Request Data
     * @return ?array
     */
    abstract public function update(array $existing): ?array;

    /*============================ INTERNAL API ============================*/
    /**
     * Match Database Columns with Queries
     * @return array
     */
    public function queries(): array
    {
        $queries = [];
        $inputs = $this->request->inputs();
        // Get Accepted Query Values
        foreach($inputs as $k => $v) {
            if (in_array($k, $this->acceptedQueries)) {
                $queries[$k] = $v;
            }
        }
        return $queries;
    }
}