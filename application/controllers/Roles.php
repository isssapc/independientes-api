<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Roles extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("rol");
    }

    public function index_get() {
        $datos = $this->rol->get_all();
        $this->response($datos);
    }

    public function get_rol_get($id) {
        $datos = $this->rol->get_one($id);
        $this->response($datos);
    }

    public function del_rol_post($id) {
        $datos = $this->rol->del_one($id);
        $this->response($datos);
    }

    public function del_roles_post() {
        $ids = $this->post("id_roles");
        $datos = $this->rol->del_many($ids);
        $this->response($datos);
    }

    public function create_rol_post() {
        $rol = $this->post("rol");
        $datos = $this->rol->create_one($rol);
        $this->response($datos);
    }

    public function update_rol_post() {
        $rol = $this->post("rol");
        $datos = $this->rol->update_one($rol);
        $this->response($datos);
    }

}
