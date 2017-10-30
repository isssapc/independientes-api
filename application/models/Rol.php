<?php

class Rol extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {

        $sql = "SELECT *
                FROM rol";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_one($id) {

        $sql = "SELECT r.*
                FROM rol r
                WHERE r.id_rol= $id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function del_one($id) {

        $sql = "SELECT r.*
                FROM rol r
                WHERE r.id_rol= $id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function del_many($ids) {

        $sql = "SELECT r.*
                FROM rol r
                WHERE r.id_rol= $id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function create_one($rol) {

        $sql = "SELECT r.*
                FROM rol r
                WHERE r.id_rol= $id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function update_one($rol) {

        $sql = "SELECT r.*
                FROM rol r
                WHERE r.id_rol= $id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}
