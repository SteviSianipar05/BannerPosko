<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    protected $table = 'admin';

    public function get_all() {
        return $this->db->order_by('id', 'ASC')->get($this->table)->result();
    }

    public function get_by_id($id) {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function get_by_username($username) {
        return $this->db->where('username', $username)->get($this->table)->row();
    }

    public function username_exists($username) {
        return $this->db->where('username', $username)->count_all_results($this->table) > 0;
    }

    public function insert($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function delete($id) {
        $this->db->where('id', $id)->delete($this->table);
    }
}