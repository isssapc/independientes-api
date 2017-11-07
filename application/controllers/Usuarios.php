<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends MY_Controller {
    private $database;

    public function __construct() {
        parent::__construct();

        //en el parent parent::__construct se ejecutan los operaciones de middleware
        $this->database = $this->middlewares['auth']->claims["db"];
       

        $this->load->model("usuario");
    }

    protected function middleware() {
        /**
         * Return the list of middlewares you want to be applied,
         * Here is list of some valid options
         *
         * admin_auth                    // As used below, simplest, will be applied to all
         * someother|except:index,list   // This will be only applied to posts()
         * yet_another_one|only:index    // This will be only applied to index()
         * */
        return array('auth');
    }

    public function index_get() {
        $datos = $this->usuario->get_all($this->database);
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
        $usuario["db"]= $this->database;
        $datos = $this->usuario->create_one($usuario);
        $this->response($datos);
    }

    public function update_usuario_post($id) {
        $usuario = $this->post("usuario");
        $datos = $this->usuario->update_one($id, $usuario);
        $this->response($datos);
    }

}
