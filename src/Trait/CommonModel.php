<?php

declare(strict_types=1);

namespace LBM\Trait;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\App\model\Address;

trait CommonModel
{
    /**
     * @var mixed $result
     */
    protected mixed $result = null;

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
        $this->result = $this->assignStatus();
        return $this;
    }

    /**
     * Get Address
     * @param string $type Address Type. Accepted: 'client' or 'staff'
     * @return self
     */
    public function address(string $type): self
    {
        $this->result = $this->assignAddress($type);
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
    public function result(): mixed
    {
        $result = $this->result;
        // Reset Values
        $this->result = null;
        $this->select = null;
        // Return Result
        return $result;
    }

    /* ====================================================================================== */
    /**
     * Make Status Relation
     * @return mixed
     */
    private function assignStatus(): mixed
    {
        // Check Result is Not Empty
        if (empty($this->result)) {
            return $this->result;
        }
        // Get Status Model
        $statuses = $this->statuses();

        // Set Status
        if (isset($this->result['status'])) {
            $this->result['status'] = [
                'entity' => $this->result['status'],
                'color' => $statuses[$this->result['status']] ?? '#000000'
                ];
        } elseif (isset($this->result[0]['status'])) {
            $keys = array_keys($this->result);
            foreach ($keys as $k) {
                $this->result[$k]['status'] = [
                    'entity' => $this->result[$k]['status'],
                    'color' => $statuses[$this->result[$k]['status']] ?? '#000000'
                    ];
            }
        }
        return $this->result;
    }

    /**
     * Assign Address
     * @return mixed
     */
    private function assignAddress(string $type): mixed
    {
        // Check Result is Not Empty
        if (empty($this->result)) {
            return $this->result;
        }

        // Validate Type
        $type = strtolower($type);
        if (!in_array($type, ['client', 'staff'])) {
            return $this->result;
        }

        // Get Address Model
        $obj = new Address();

        // Assign Address
        if (isset($this->result[$this->id])) {
            $where = ['relid' => $this->result[$this->id], 'type' => $type, 'profile_default' => 'yes'];
            $this->result['address'] = $obj->where($where)->first();
        } elseif (isset($this->result[0][$this->id])) {
            $keys = array_keys($this->result);
            foreach ($keys as $k) {
                $where = ['relid' => $this->result[$k][$this->id], 'type' => $type, 'profile_default' => 'yes'];
                $this->result[$k]['address'] = $obj->where($where)->first();
            }
        }
        return $this->result;
    }
}
