<?php

class Lote extends CI_Model {

    public function __construct() {
        parent::__construct();
        $timezone = 'America/Mexico_City';
        date_default_timezone_set($timezone);
    }

    public function get_all() {

        $sql = "SELECT *
                FROM lote";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_one($id) {

        $sql = "SELECT l.*
                FROM lote l
                WHERE l.id_lote= $id LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function search_by_nombre($nombre) {
        $sql = "SELECT *
                FROM lote l 
                WHERE l.nombre like '%$nombre%'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function del_one($id) {

        $this->db->where('id_lote', $id);
        $this->db->delete('lote');
        $count = $this->db->affected_rows();
        return $count;
    }

    /*
     * 
     * TODO
     */

    public function del_many($ids) {

        $sql = "SELECT l.*
                FROM lote l
                WHERE l.id_lote= $ids";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function create_one($lote) {

        $now = date("Y-m-d H:i:s");
        $lote["fecha"] = $now;

        $this->db->insert('lote', $lote);
        $id_lote = $this->db->insert_id();

        $lote = $this->get_one($id_lote);
        return $lote;
    }

    public function update_one($id, $props) {

        $where = "id_lote = $id";
        $sql = $this->db->update_string('lote', $props, $where);
        $this->db->query($sql);

        $lote = $this->get_one($id);
        return $lote;
    }

}
