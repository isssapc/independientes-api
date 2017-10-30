<?php

class Equipo extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {

        $sql = "SELECT *
                FROM equipo";
        $query = $this->db->query($sql);
        return $query->result_array();
    }


    public function get_equipos_liga($id_liga) {

        $sql = "SELECT *
                FROM equipo
                WHERE id_liga=$id_liga";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_one($id) {

        $sql = "SELECT e.*
                FROM equipo e
                WHERE e.id_equipo= $id LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function search_by_nombre($nombre) {
        $sql = "SELECT *
                FROM equipo e 
                WHERE e.nombre like '%$nombre%'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function del_one($id) {

        $this->db->where('id_equipo', $id);
        $this->db->delete('equipo');
        $count = $this->db->affected_rows();
        return $count;
    }

    /*
     * 
     * TODO
     */

    public function del_many($ids) {

        $sql = "SELECT t.*
                FROM equipo e
                WHERE c.id_equipo= $ids";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function create_one($equipo) {

        $this->db->insert('equipo', $equipo);
        $id_equipo = $this->db->insert_id();

        $equipo = $this->get_one($id_equipo);
        return $equipo;
    }

    public function update_one($id, $props) {

        $where = "id_equipo = $id";
        $sql = $this->db->update_string('equipo', $props, $where);
        $this->db->query($sql);

        $equipo = $this->get_one($id);
        return $equipo;
    }

}
