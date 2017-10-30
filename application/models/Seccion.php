<?php

class Seccion extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {

        $sql = "SELECT *
                FROM seccion";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_one($id) {

        $sql = "SELECT s.*
                FROM seccion s
                WHERE s.id_seccion= $id LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function search_by_nombre($nombre) {
        $sql = "SELECT *
                FROM seccion s 
                WHERE s.nombre like '%$nombre%'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function del_one($id) {

        $this->db->where('id_seccion', $id);
        $this->db->delete('seccion');
        $count = $this->db->affected_rows();
        return $count;
    }

    /*
     * 
     * TODO
     */

    public function del_many($ids) {

        $sql = "SELECT t.*
                FROM seccion s
                WHERE s.id_seccion= $ids";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function create_one($seccion) {

        $this->db->insert('seccion', $seccion);
        $id_seccion = $this->db->insert_id();

        $seccion = $this->get_one($id_seccion);
        return $seccion;
    }

    public function update_one($id, $props) {

        $where = "id_seccion = $id";
        $sql = $this->db->update_string('seccion', $props, $where);
        $this->db->query($sql);

        $seccion = $this->get_one($id);
        return $seccion;
    }

}
