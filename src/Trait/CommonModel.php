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

namespace LBM\Trait;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

trait CommonModel
{
    /**
     * @var mixed $result
     */
    protected mixed $result = null;

    /**
     * Get Rows
     * @param array $where Where Array to Get Rows. Example: ['id'=>1]
     * @param ?string $columns Table columns. Example: 'id,uuid,username'
     * @param string $operator Where Clause Operator. Example: '='
     * @param string $compare Where Clause Compare. Example: 'AND'
     * @return self
     */
    public function rows(
        array $where = [],
        ?string $columns = null,
        string $operator = '=',
        string $compare = 'AND',
        int|string $page = 1
    ): self
    {
        $limit = do_hook('option', 'page.limit', 20);
        $this->result = $this->select($columns)
                            ->where($where, $operator, $compare)
                            ->limit($limit)
                            ->offset($page)
                            ->get();
        return $this;
    }

    /**
     * Get Row
     * @param array $where Where Array. Example: ['id' => 1, 'uuid' => 'uuid-sdfa-sdffsff-ewrf34']
     * @param ?string $columns Table columns. Example: 'id,uuid,username'
     * @return self
     */
    public function row(array $where, ?string $columns = null): self
    {
        $this->result = $this->select($columns)->where($where)->first();
        return $this;
    }

    /**
     * Get Status
     * @param ?string $columns Table columns. Example: 'entity,color'
     * @return self
     */
    public function status(string $model, ?string $columns = null): self
    {
        if (empty($this->result)) {
            return $this;
        }

        // Status Model
        $model = new $model();
        // Set Status
        if (isset($this->result['status'])) {
            $columns = $columns ?: 'entity, color';
            $this->result['status'] = $model->select($columns)->where(['entity' => $this->result['status']])->first();
            return $this;
        }
        // Set Statuses
        if (isset($this->result[0]['status'])) {
            foreach ($this->result as $k => $v) {
                $this->result[$k]['status'] = $model->select($columns)->where(['entity' => $this->result[$k]['status']])->first();
            }
        }
        return $this;
    }

    /**
     * Get Result
     * @return mixed
     */
    public function result()
    {
        $result = $this->result;
        $this->result = null;
        return $result;
    }
}
