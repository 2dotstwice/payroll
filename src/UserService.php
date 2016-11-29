<?php
namespace TwoDotsTwice\Payroll;

class UserService extends BaseService
{
    public function getOne($id)
    {
        return $this->db->fetchAssoc('SELECT * FROM users WHERE id=?', [(int) $id]);
    }
    public function getAll()
    {
        return $this->db->fetchAll('SELECT * FROM users');
    }
    public function save($user)
    {
        $this->db->insert("users", $user);
        return $this->db->lastInsertId();
    }
    public function update($id, $user)
    {
        return $this->db->update('users', $user, ['id' => $id]);
    }
    public function delete($id)
    {
        return $this->db->delete("users", array("id" => $id));
    }
}
