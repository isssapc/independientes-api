<?php

class Colonia extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {

        $sql = "SELECT *
                FROM colonia";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_count() {

        return $this->db->count_all('colonia');
    }

    public function get_page($pageSize, $page) {

        $pageSize = intval($pageSize);
        $page = intval($page);
        $offset = $pageSize * $page;

        $sql = "SELECT *
                FROM colonia LIMIT $offset,$pageSize";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_one($id) {

        $sql = "SELECT c.*
                FROM colonia c
                WHERE c.id_colonia= $id LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function search_by_nombre($nombre) {
        $sql = "SELECT *
                FROM colonia c 
                WHERE c.nombre like '%$nombre%'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function del_one($id) {

        $this->db->where('id_colonia', $id);
        $this->db->delete('colonia');
        $count = $this->db->affected_rows();
        return $count;
    }

    public function del_all() {

        $this->db->from('colonia');
        $this->db->truncate();

        $count = $this->db->count_all('colonia');
        return array('count' => $count);
    }

    /*
     * 
     * TODO
     */

    public function del_many($ids) {

        $sql = "SELECT t.*
                FROM colonia c
                WHERE c.id_colonia= $ids";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function create_one($colonia) {

        $this->db->insert('colonia', $colonia);
        $id_colonia = $this->db->insert_id();

        $colonia = $this->get_one($id_colonia);
        return $colonia;
    }

    public function create_many($colonias) {

        $this->db->insert_batch('colonia', $colonias);
        $count = $this->db->affected_rows();
        return array("count" => $count);
        //$id_colonia = $this->db->insert_id();
        //$colonia = $this->get_one($id_colonia);
        //return $colonia;
    }

    public function update_one($id, $props) {

        $where = "id_colonia = $id";
        $sql = $this->db->update_string('colonia', $props, $where);
        $this->db->query($sql);

        $colonia = $this->get_one($id);
        return $colonia;
    }

}
