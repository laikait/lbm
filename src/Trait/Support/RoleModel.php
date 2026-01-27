<?php

declare(strict_types=1);

namespace LBM\Trait\Support;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\App\Model\StaffRole as StaffRoleModel;

trait RoleModel
{
    /**
     * Assign Staff Roles
     * @return self
     */
    public function role(): self
    {
        // Check Result is Not Empty
        if (empty($this->result)) {
            return $this;
        }

        // Get Role Class
        $class = __CLASS__ . 'Role';
        if (!class_exists($class)) {
            return $this;
        }

        $obj = new $class();


        // Set Roles
        if (isset($this->result['role'])) {
            $role = $obj->select('type, entities')->where(['type' => $this->result['role']])->first();
            $role['entities'] = unserialize($role['entities']);
            $this->result['role'] = $role;
        } elseif (isset($this->result[0]['role'])) {
            $keys = array_keys($this->result);
            foreach ($keys as $k) {
                $this->result[$k]['role'] = $obj->select('type, entities')->where(['type' => $this->result[$k]['role']])->first();
            }
        }
        return $this;
    }
}
