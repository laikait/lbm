<?php

declare(strict_types=1);

namespace LBM\Trait\Support;

// Deny Direct Access
defined('APP_PATH') || http_response_code(403).die('403 Direct Access Denied!');

use Laika\App\Model\Staff as StaffModel;

trait NotesModel
{
    /**
     * Assign Staff Notes
     * @return self
     */
    public function staffNote(): self
    {
        // Check Result is Not Empty
        if (empty($this->result)) {
            return $this;
        }

        // Get Note Class
        $class = __CLASS__ . 'Note';
        if (!class_exists($class)) {
            return $this;
        }
        $obj = new $class();

        // Set Notes
        if (isset($this->result[$this->id])) {
            $where = ['relid' => $this->result[$this->id]];
            $notes = $obj->select('uuid,staff,note,created')->where($where)->order($obj->id, 'DESC')->get();
            $this->result['notes'] = $this->assignNotesStaff($notes);
        } elseif (isset($this->result[0][$this->id])) {
            $keys = array_keys($this->result);
            foreach ($keys as $k) {
                $where = ['relid' => $this->result[$k][$this->id]];
                $notes = $obj->select('uuid,staff,note,created')->where($where)->order($obj->id, 'DESC')->get();
                $this->result[$k]['notes'] = $this->assignNotesStaff($notes);
            }
        }
        return $this;
    }

    /**
     * Assign Client Notes
     * @return self
     */
    public function clientNote(): self
    {
        // Check Result is Not Empty
        if (empty($this->result)) {
            return $this;
        }

        // Get Note Class
        $class = __CLASS__ . 'Note';
        $obj = new $class();

        // Set Notes
        if (isset($this->result[$this->id])) {
            $where = ['relid' => $this->result[$this->id]];
            $notes = $obj->where($where)->order($obj->id, 'DESC')->get();
            $this->result['notes'] = $this->assignNotesStaff($notes);
        } elseif (isset($this->result[0][$this->id])) {
            $keys = array_keys($this->result);
            foreach ($keys as $k) {
                $where = ['relid' => $this->result[$k][$this->id]];
                $notes = $obj->where($where)->order($obj->id, 'DESC')->get();
                $this->result[$k]['notes'] = $this->assignNotesStaff($notes);
            }
        }
        return $this;
    }

    /**
     * Assign Note Staffs
     * @return array
     */
    private function assignNotesStaff(array $notes): array
    {
        $model = new StaffModel();
        foreach ($notes as $k => $note) {
            $notes[$k]['staff'] = $model->row([$model->id => $note['staff']], columns:'uuid,username')->result();
        }
        return $notes;
    }
}
