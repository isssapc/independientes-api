<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Secciones extends MY_Controller {

    public function __construct() {
        parent::__construct();

        //en el parent parent::__construct se ejecutan los operaciones de middleware
        $database = $this->middlewares['auth']->claims["db"];
        $this->db->db_select($database);

        $this->load->model("seccion");
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
        $datos = $this->seccion->get_all();
        $this->response($datos);
    }


    public function get_seccion_get($id) {
        $datos = $this->seccion->get_one($id);
        $this->response($datos);
    }

    public function search_seccion_get($nombre) {
        $datos = $this->seccion->search_by_nombre($nombre);
        $this->response($datos);
    }

    public function del_seccion_post($id) {
        $count = $this->seccion->del_one($id);
        $this->response(array("count" => $count));
    }

    public function del_seccions_post() {
        $ids = $this->post("id_seccions");
        $datos = $this->seccion->del_many($ids);
        $this->response($datos);
    }

    public function create_seccion_post() {
        $seccion = $this->post("seccion");
        $datos = $this->seccion->create_one($seccion);
        $this->response($datos);
    }

    public function update_seccion_post($id) {
        $seccion = $this->post("seccion");
        $datos = $this->seccion->update_one($id, $seccion);
        $this->response($datos);
    }

}
