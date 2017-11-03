<?php

class Meta extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {

        $sql = "SELECT *
                FROM meta
                WHERE nombre = 'CONFIGURACION' LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function get_one($id) {

        $sql = "SELECT *
                FROM meta
                WHERE nombre = 'CONFIGURACION' LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function del_one($id) {

        $this->db->where('id_meta', $id);
        $this->db->delete('meta');
        $count = $this->db->affected_rows();
        return $count;
    }

    public function create_one($meta) {

        $this->db->insert('meta', $meta);
        $id_meta = $this->db->insert_id();

        $meta = $this->get_one($id_meta);
        return $meta;
    }

    public function update_one($props) {

        //$where = "id_meta = $id";
        $where = "nombre= 'CONFIGURACION'";
        $sql = $this->db->update_string('meta', $props, $where);
        $this->db->query($sql);

        $meta = $this->get_one(1);
        return $meta;
    }

}
