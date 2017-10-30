<?php

class Usuario extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {

        $sql = "SELECT u.*, t.tipo AS tipo_usuario
                FROM usuario u
                JOIN tipo_usuario t ON t.id_tipo_usuario=u.id_tipo_usuario";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_roles() {

        $sql = "SELECT *
                FROM tipo_usuario";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_one($id) {

        $sql = "SELECT u.*, t.tipo AS tipo_usuario
                FROM usuario u
                JOIN tipo_usuario t ON t.id_tipo_usuario= u.id_tipo_usuario
                WHERE u.id_usuario= $id LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function get_by_email($email) {
        $sql = "SELECT u.*
                FROM usuario u                
                WHERE u.email= '$email' LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function del_one($id) {

        $sql = "DELETE FROM usuario WHERE id_usuario=$id LIMIT 1";
        $this->db->query($sql);
        $count = $this->db->affected_rows();
        return $count;
    }

    public function del_many($ids) {

        $this->db->where_in('id_usuario', $ids);
        $this->db->delete('usuario');
        $count = $this->db->affected_rows();
        return $count;
    }

    public function create_one($usuario) {

        $this->db->insert('usuario', $usuario);
        $id_usuario = $this->db->insert_id();

        $usuario = $this->get_one($id_usuario);
        return $usuario;
    }

    public function update_one($id, $props) {

        $where = "id_usuario = $id";
        $sql = $this->db->update_string('usuario', $props, $where);
        $this->db->query($sql);

        $usuario = $this->get_one($id);
        return $usuario;
    }

}
