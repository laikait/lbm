<?php

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
     * @param string $operator Where Clause Operator. Example: '='
     * @param string $compare Where Clause Compare. Example: 'AND'
     * @param int|string $page Page Number. Default is 1
     * @param int|string $limit DB Rows Limit. Default is 20
     * @param ?string $columns Table columns. Example: 'id,uuid,username'
     * @return self
     */
    public function rows(
        array $where = [],
        string $operator = '=',
        string $compare = 'AND',
        int|string $page = 1,
        int|string|null $limit = null,
        ?string $columns = null
    ): self
    {
        $limit = \do_hook('option', 'data.limit', 20);
        $model = $this->select($columns)->where($where, $operator, $compare);
        if ($limit !== null) {
            $model = $model->limit((int) $limit)->offset($page);
        }
        $this->result = $model->get();
        return $this;
    }

    /**
     * Get Rows by Order
     * @param array $where Where Array to Get Rows. Example: ['id'=>1]
     * @param string $operator Where Clause Operator. Example: '='
     * @param string $compare Where Clause Compare. Example: 'AND'
     * @param string $by Order By Column Name. Example: 'id'
     * @param string $order Order Type. Accepted: 'ASC/DESC'
     * @param ?string $columns Table columns. Example: 'id,uuid,username'
     * @return self
     */
    public function rowsByOrder(
        array $where = [],
        string $operator = '=',
        string $compare = 'AND',
        string $by = 'id',
        string $order = 'ASC',
        ?string $columns = null
    ): self
    {
        $limit = \do_hook('option', 'page.limit', 20);
        $this->result = $this->select($columns)
                            ->where($where, $operator, $compare)
                            ->limit($limit)
                            ->orderBy($by, $order)
                            ->get();
        return $this;
    }

    /**
     * Get Row
     * @param array $where Where Array. Example: ['id' => 1, 'uuid' => 'uuid-sdfa-sdffsff-ewrf34']
     * @param string $operator Where Clause Operator. Example: '='
     * @param string $compare Where Clause Compare. Example: 'AND'
     * @param ?string $columns Table columns. Example: 'id,uuid,username'
     * @return self
     */
    public function row(
        array $where,
        string $operator = '=',
        string $compare = 'AND',
        ?string $columns = null
    ): self
    {
        $this->result = $this->select($columns)->where($where, $operator, $compare)->first();
        return $this;
    }

    /**
     * Get Status
     * @param ?string $columns Table columns. Example: 'entity,color'
     * @return self
     */
    public function status(?string $columns = null): self
    {
        if (empty($this->result)) {
            return $this;
        }

        // Get Status Model
        $class = __CLASS__ . 'Status';
        if (!class_exists($class)) {
            return $this;
        }
        $obj = new $class();

        // Set Status
        $columns = $columns ?: 'entity, color';
        if (isset($this->result['status'])) {
            $this->result['status'] = $obj->select($columns)->where(['entity' => $this->result['status']])->first();
        } elseif (isset($this->result[0]['status'])) {
            foreach ($this->result as $k => $v) {
                $this->result[$k]['status'] = $obj->select($columns)->where(['entity' => $this->result[$k]['status']])->first();
            }
        }
        return $this;
    }

    /**
     * Get Statuses With Colors
     * @return array
     */
    public function statuses(): array
    {
        $class = __CLASS__ . 'Status';
        if (!class_exists($class)) {
            return [];
        }
        $model = new $class;
        $statuses = $model->select('entity,color')->get();
        return array_column($statuses, 'color', 'entity');
    }

    // /**
    //  * Get Notes
    //  * @return array
    //  */
    // public function relatedNotes(int|string $id): array
    // {
    //     $class = __CLASS__ . 'Note';
    //     if (!class_exists($class)) {
    //         return [];
    //     }
    //     // Get Limit
    //     $model = new $class;
    //     return $model->where(['relid' => $id])->order($model->id, 'DESC')->get();
    // }

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
