<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("usuario");
    }

    public function index_get() {
        $datos = $this->usuario->get_all();
        $this->response($datos);
    }

    public function get_usuario_get($id) {
        $datos = $this->usuario->get_one($id);
        $this->response($datos);
    }

    public function get_roles_get() {
        $datos = $this->usuario->get_roles();
        $this->response($datos);
    }

    public function del_usuario_post($id) {
        $count = $this->usuario->del_one($id);
        $this->response(array("count" => $count));
    }

    public function del_usuarios_post() {
        $ids = $this->post("id_usuarios");
        $datos = $this->usuario->del_many($ids);
        $this->response($datos);
    }

    public function create_usuario_post() {
        $usuario = $this->post("usuario");
        $datos = $this->usuario->create_one($usuario);
        $this->response($datos);
    }

    public function update_usuario_post($id) {
        $usuario = $this->post("usuario");
        $datos = $this->usuario->update_one($id, $usuario);
        $this->response($datos);
    }

}
