<?php

class User_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_users()
    {
        $this->db->select("*");
        $this->db->from("users");
        $query = $this->db->get();

        return $query->result();
    }

    public function insert_user($data = array())
    {
        return $this->db->insert("users", $data);
    }

    public function insert_password($id, $password)
    {
        $update_rows = array('password' => md5($password));
        $this->db->where('id', $id);
        return $this->db->update('users', $update_rows);
    }

    public function is_user_exists($id)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }

    public function get_user($email, $password)
    {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('email', $email);
        $this->db->where('password', md5($password));
        $query = $this->db->get();
        $result = $query->result_array();
        return $result;
    }
}
