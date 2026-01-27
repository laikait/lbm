<?php

declare(strict_types=1);

namespace LBM\Trait\Support;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

trait AddressModel
{
    /**
     * Assign Address
     * @param string $type Address Type. Accepted: 'client', staff'
     * @return self
     */
    public function address(string $type): self
    {
        // Check Result is Not Empty
        if (empty($this->result)) {
            return $this;
        }

        // Get Address Model
        $class = '\\Laika\\App\\Model\\Address';
        $obj = new $class();
    
        // Set Status
        if (isset($this->result[$this->id])) {
            $where = [
                'relid' => $this->result[$this->id],
                'type' => strtolower($type),
                'profile_default' => 'yes'
            ];
            $this->result['address'] = $obj->select('address_1,address_2,city,zip,country,profile_default')->where($where)->first();
        } elseif (isset($this->result[0][$this->id])) {
            $keys = array_keys($this->result);
            foreach ($keys as $k) {
                $where = [
                    'relid' => $this->result[$k][$this->id],
                    'type' => strtolower($type),
                    'profile_default' => 'yes'
                ];
                $this->result[$k]['address'] = $obj->where($where)->first();
            }
        }
        return $this;
    }
}
