<?php

class Meta extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {

        $sql = "SELECT *
                FROM meta";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_one($id) {

        $sql = "SELECT m.*
                FROM meta m
                WHERE m.id_meta= $id LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function search_by_nombre($nombre) {
        $sql = "SELECT *
                FROM meta m 
                WHERE m.nombre like '%$nombre%'";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function del_one($id) {

        $this->db->where('id_meta', $id);
        $this->db->delete('meta');
        $count = $this->db->affected_rows();
        return $count;
    }

    /*
     * 
     * TODO
     */

    public function del_many($ids) {

        $sql = "SELECT m.*
                FROM meta m
                WHERE m.id_meta= $ids";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function create_one($meta) {

        $this->db->insert('meta', $meta);
        $id_meta = $this->db->insert_id();

        $meta = $this->get_one($id_meta);
        return $meta;
    }

    public function update_one($id, $props) {

        $where = "id_meta = $id";
        $sql = $this->db->update_string('meta', $props, $where);
        $this->db->query($sql);

        $meta = $this->get_one($id);
        return $meta;
    }

}
