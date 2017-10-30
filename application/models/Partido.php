<?php

class Partido extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {

        $sql = "SELECT *
                FROM partido";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_partidos_jornada($id_jornada) {

        $sql = "SELECT p.*,e1.nombre AS local, e2.nombre AS visitante
                FROM partido p
                JOIN equipo e1 ON e1.id_equipo= p.id_equipo_local
                JOIN equipo e2 ON e2.id_equipo= p.id_equipo_visitante
                WHERE p.id_jornada= $id_jornada";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_one($id) {

        $sql = "SELECT p.*,e1.nombre AS local, e2.nombre AS visitante
                FROM partido p
                JOIN equipo e1 ON e1.id_equipo= p.id_equipo_local
                JOIN equipo e2 ON e2.id_equipo= p.id_equipo_visitante
                WHERE p.id_partido= $id LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function search_by_nombre($nombre) {
        $sql = "SELECT *
                FROM partido p 
                WHERE p.nombre like '%$nombre%'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function del_one($id) {

        $this->db->where('id_partido', $id);
        $this->db->delete('partido');
        $count = $this->db->affected_rows();
        return $count;
    }

    /*
     * 
     * TODO
     */

    public function del_many($ids) {

        $sql = "SELECT t.*
                FROM partido p
                WHERE c.id_partido= $ids";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function create_one($partido) {

        $this->db->insert('partido', $partido);
        $id_partido = $this->db->insert_id();

        $partido = $this->get_one($id_partido);
        return $partido;
    }

    public function update_one($id, $props) {

        $where = "id_partido = $id";
        $sql = $this->db->update_string('partido', $props, $where);
        $this->db->query($sql);

        $partido = $this->get_one($id);
        return $partido;
    }

}
