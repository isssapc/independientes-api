<?php

class Jugador extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {

        $sql = "SELECT *
                FROM jugador";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_jugadores_equipo($id_equipo) {

        $sql = "SELECT *
                FROM jugador
                WHERE id_equipo= $id_equipo";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_one($id) {

        $sql = "SELECT j.*
                FROM jugador j
                WHERE j.id_jugador= $id LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function search_by_nombre($nombre) {
        $sql = "SELECT *
                FROM jugador j 
                WHERE j.nombre like '%$nombre%'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function del_one($id) {

        $this->db->where('id_jugador', $id);
        $this->db->delete('jugador');
        $count = $this->db->affected_rows();
        return $count;
    }

    /*
     * 
     * TODO
     */

    public function del_many($ids) {

        $sql = "SELECT t.*
                FROM jugador j
                WHERE c.id_jugador= $ids";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function create_one($jugador) {

        $this->db->insert('jugador', $jugador);
        $id_jugador = $this->db->insert_id();

        $jugador = $this->get_one($id_jugador);
        return $jugador;
    }

    public function update_one($id, $props) {

        $where = "id_jugador = $id";
        $sql = $this->db->update_string('jugador', $props, $where);
        $this->db->query($sql);

        $jugador = $this->get_one($id);
        return $jugador;
    }

}
