<?php

class Liga extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {

        $sql = "SELECT *
                FROM liga";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_ligas_temporada($id_temporada) {

        $sql = "SELECT *
                FROM liga
                WHERE id_temporada=$id_temporada";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_one($id) {

        $sql = "SELECT l.*
                FROM liga l
                WHERE l.id_liga= $id LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function search_by_nombre($nombre) {
        $sql = "SELECT *
                FROM liga l 
                WHERE l.nombre like '%$nombre%'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function del_one($id) {

        $this->db->where('id_liga', $id);
        $this->db->delete('liga');
        $count = $this->db->affected_rows();
        return $count;
    }

    /*
     * 
     * TODO
     */

    public function del_many($ids) {

        $sql = "SELECT t.*
                FROM liga l
                WHERE c.id_liga= $ids";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function create_one($liga) {

        $this->db->insert('liga', $liga);
        $id_liga = $this->db->insert_id();

        $liga = $this->get_one($id_liga);
        return $liga;
    }

    public function update_one($id, $props) {

        $where = "id_liga = $id";
        $sql = $this->db->update_string('liga', $props, $where);
        $this->db->query($sql);

        $liga = $this->get_one($id);
        return $liga;
    }

}
