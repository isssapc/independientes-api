<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Jornadas extends MY_Controller {

    public function __construct() {
        parent::__construct();

        //en el parent parent::__construct se ejecutan los operaciones de middleware
        $database = $this->middlewares['auth']->claims["db"];
        $this->db->db_select($database);

        $this->load->model("jornada");
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
        $datos = $this->jornada->get_all();
        $this->response($datos);
    }

//    public function get_jornadas_liga_get($id_liga) {
//        $datos = $this->jornada->get_jornadas_liga($id_liga);
//        $this->response($datos);
//    }

    public function get_jornada_get($id) {
        $datos = $this->jornada->get_one($id);
        $this->response($datos);
    }

    public function search_jornada_get($nombre) {
        $datos = $this->jornada->search_by_nombre($nombre);
        $this->response($datos);
    }

    public function del_jornada_post($id) {
        $count = $this->jornada->del_one($id);
        $this->response(array("count" => $count));
    }

    public function del_jornadas_post() {
        $ids = $this->post("id_jornadas");
        $datos = $this->jornada->del_many($ids);
        $this->response($datos);
    }

    public function create_jornada_post() {
        $jornada = $this->post("jornada");
        $datos = $this->jornada->create_one($jornada);
        $this->response($datos);
    }

    public function update_jornada_post($id) {
        $jornada = $this->post("jornada");
        $datos = $this->jornada->update_one($id, $jornada);
        $this->response($datos);
    }

}
