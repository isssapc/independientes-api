<?php

class Temporada extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {

        $sql = "SELECT *
                FROM temporada";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_one($id) {

        $sql = "SELECT t.*
                FROM temporada t
                WHERE t.id_temporada= $id LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function search_by_nombre($nombre) {
        $sql = "SELECT *
                FROM temporada t 
                WHERE c.nombre like '%$nombre%'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function del_one($id) {

        $this->db->where('id_temporada', $id);
        $this->db->delete('temporada');
        $count = $this->db->affected_rows();
        return $count;
    }

    /*
     * 
     * TODO
     */

    public function del_many($ids) {

        $sql = "SELECT t.*
                FROM temporada t
                WHERE c.id_temporada= $ids";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function create_one($temporada) {

        $this->db->insert('temporada', $temporada);
        $id_temporada = $this->db->insert_id();

        $temporada = $this->get_one($id_temporada);
        return $temporada;
    }

    public function update_one($id, $props) {

        $where = "id_temporada = $id";
        $sql = $this->db->update_string('temporada', $props, $where);
        $this->db->query($sql);

        $temporada = $this->get_one($id);
        return $temporada;
    }

}
