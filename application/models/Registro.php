<?php

class Registro extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {

        $sql = "SELECT *
                FROM registro";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_registroes_equipo($id_equipo) {

        $sql = "SELECT *
                FROM registro
                WHERE id_equipo= $id_equipo";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_one($id) {

        $sql = "SELECT r.*
                FROM registro r
                WHERE r.id_registro= $id LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function search_by_nombre($nombre) {
        $sql = "SELECT *
                FROM registro r 
                WHERE r.nombre like '%$nombre%'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function del_one($id) {

        $this->db->where('id_registro', $id);
        $this->db->delete('registro');
        $count = $this->db->affected_rows();
        return $count;
    }

    /*
     * 
     * TODO
     */

    public function del_many($ids) {

        $sql = "SELECT t.*
                FROM registro r
                WHERE r.id_registro= $ids";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function create_one($registro) {

        $this->db->insert('registro', $registro);
        $id_registro = $this->db->insert_id();

        $registro = $this->get_one($id_registro);
        return $registro;
    }

    public function update_one($id, $props) {

        $where = "id_registro = $id";
        $sql = $this->db->update_string('registro', $props, $where);
        $this->db->query($sql);

        $registro = $this->get_one($id);
        return $registro;
    }

}
