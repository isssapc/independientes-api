<?php

class Jornada extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {

        $sql = "SELECT *
                FROM jornada";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_jornadas_liga($id_liga) {

        $sql = "SELECT *
                FROM jornada
                WHERE id_liga=$id_liga";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_one($id) {

        $sql = "SELECT j.*
                FROM jornada j
                WHERE j.id_jornada= $id LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function search_by_nombre($nombre) {
        $sql = "SELECT *
                FROM jornada j 
                WHERE j.nombre like '%$nombre%'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function del_one($id) {

        $this->db->where('id_jornada', $id);
        $this->db->delete('jornada');
        $count = $this->db->affected_rows();
        return $count;
    }

    /*
     * 
     * TODO
     */

    public function del_many($ids) {

        $sql = "SELECT t.*
                FROM jornada j
                WHERE c.id_jornada= $ids";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function create_one($jornada) {

        $this->db->insert('jornada', $jornada);
        $id_jornada = $this->db->insert_id();

        $jornada = $this->get_one($id_jornada);
        return $jornada;
    }

    public function update_one($id, $props) {

        $where = "id_jornada = $id";
        $sql = $this->db->update_string('jornada', $props, $where);
        $this->db->query($sql);

        $jornada = $this->get_one($id);
        return $jornada;
    }

}
