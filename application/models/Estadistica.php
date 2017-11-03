<?php

class Estadistica extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {

        $sql = "SELECT *
                FROM estadistica";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_one($id) {

        $sql = "SELECT e.*
                FROM estadistica e
                WHERE e.id_estadistica= $id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function del_one($id) {

        $sql = "SELECT e.*
                FROM estadistica e
                WHERE e.id_estadistica= $id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function del_many($ids) {

        $sql = "SELECT e.*
                FROM estadistica e
                WHERE e.id_estadistica= $id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function create_one($estadistica) {

        $sql = "SELECT e.*
                FROM estadistica e
                WHERE e.id_estadistica= $id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function update_one($estadistica) {

        $sql = "SELECT e.*
                FROM estadistica e
                WHERE e.id_estadistica= $id";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

}
