<?php

declare(strict_types=1);

namespace LBM\Trait\Support;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

trait StatusModel
{
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

    /**
     * Assign Address
     * @return self
     */
    public function status(): self
    {
        // Check Result is Not Empty
        if (empty($this->result)) {
            return $this;
        }

        // Get Statuses
        $statuses = $this->statuses();

        // Set Status
        if (isset($this->result['status'])) {
            $this->result['status'] = [
                'entity' => $this->result['status'],
                'color' => $statuses[$this->result['status']]
                ];
        } elseif (isset($this->result[0]['status'])) {
            $keys = array_keys($this->result);
            foreach ($keys as $k) {
                $this->result[$k]['status'] = [
                    'entity' => $this->result[$k]['status'],
                    'color' => $statuses[$this->result[$k]['status']]
                    ];
            }
        }
        return $this;
    }
}
