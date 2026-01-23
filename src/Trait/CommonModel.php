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
     * @var bool $status
     */
    protected bool $status = false;

    /**
     * @var ?string $select
     */
    protected ?string $select = null;

    /**
     * @param ?string $select
     * @return self
     */
    public function columns(?string $select = null)
    {
        $this->select = $select;
        return $this;
    }

    /**
     * Get Rows
     * @param array $where Where Array to Get Rows. Example: ['id'=>1]
     * @param string $operator Where Clause Operator. Example: '='
     * @param string $compare Where Clause Compare. Example: 'AND'
     * @param int|string $page Page Number. Default is 1
     * @return self
     */
    public function rows(
        array $where = [],
        string $operator = '=',
        string $compare = 'AND',
        int|string $page = 1
    ): self
    {
        $limit = \do_hook('option', 'data.limit', 20);
        $this->result = $this->select($this->select)
                            ->where($where, $operator, $compare)
                            ->limit((int) $limit)
                            ->offset($page)
                            ->get();
        return $this;
    }

    /**
     * Get Rows by Order
     * @param array $where Where Array to Get Rows. Example: ['id'=>1]
     * @param string $operator Where Clause Operator. Example: '='
     * @param string $compare Where Clause Compare. Example: 'AND'
     * @param string $by Order By Column Name. Example: 'id'
     * @param string $order Order Type. Accepted: 'ASC/DESC'
     * @return self
     */
    public function rowsByOrder(
        array $where = [],
        string $operator = '=',
        string $compare = 'AND',
        string $by = 'id',
        string $order = 'ASC'
    ): self
    {
        $limit = \do_hook('option', 'page.limit', 20);
        $this->result = $this->select($this->select)
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
     * @return self
     */
    public function row(
        array $where,
        string $operator = '=',
        string $compare = 'AND'
    ): self
    {
        $this->result = $this->select($this->select)->where($where, $operator, $compare)->first();
        return $this;
    }

    /**
     * Get Status
     * @return self
     */
    public function status(): self
    {
        $this->status = true;
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
        $this->select = $this->select ?: 'entity,color';
        // Get Statuses
        $statuses = $model->select($this->select)->get();
        // Reset Trait
        $this->resetTrait();
        return array_column($statuses, 'color', 'entity');
    }

    /**
     * Get Result
     * @return mixed
     */
    public function result()
    {
        // Check Empty
        if (empty($this->result)) {
            $this->resetTrait();
            return [];
        }

        $result = $this->result;
        // Check Status Required
        if ($this->status) {
            $result = $this->statusRelation($result);
        }
        $this->resetTrait();
        return $result;
    }

    /* ====================================================================================== */
    /**
     * Make Status Relation
     * @return array
     */
    private function statusRelation(array $result): array
    {
        // Get Status Model
        $statuses = $this->statuses();

        // Set Status
        if (isset($result['status'])) {
            if (array_key_exists($result['status'], $statuses)) {
                $status = ['entity' => $result['status'], 'color' => $statuses[$result['status']]];
            } else {
                $status = ['entity' => $result['status'], 'color' => '#000000'];
            }
            $result['status'] = $status;
        } elseif (isset($result[0]['status'])) {
            foreach ($result as $k => $v) {
                if (array_key_exists($result[$k]['status'], $statuses)) {
                    $status = ['entity' => $result[$k]['status'], 'color' => $statuses[$result[$k]['status']]];
                } else {
                    $status = ['entity' => $result[$k]['status'], 'color' => '#000000'];
                }
                $result[$k]['status'] = $status;
            }
        }
        return $result;
    }

    /**
     * Reset Model
     * @return void
     */
    private function resetTrait(): void
    {
        $this->result = null;
        $this->status = false;
        $this->select = null;
    }
}
