<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Partidos extends MY_Controller {

    public function __construct() {
        parent::__construct();

        //en el parent parent::__construct se ejecutan los operaciones de middleware
        $database = $this->middlewares['auth']->claims["db"];
        $this->db->db_select($database);

        $this->load->model("partido");
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
        $datos = $this->partido->get_all();
        $this->response($datos);
    }

    public function get_partidos_jornada_get($id_jornada) {
        $datos = $this->partido->get_partidos_jornada($id_jornada);
        $this->response($datos);
    }

    public function get_partido_get($id) {
        $datos = $this->partido->get_one($id);
        $this->response($datos);
    }

    public function search_partido_get($nombre) {
        $datos = $this->partido->search_by_nombre($nombre);
        $this->response($datos);
    }

    public function del_partido_post($id) {
        $count = $this->partido->del_one($id);
        $this->response(array("count" => $count));
    }

    public function del_partidos_post() {
        $ids = $this->post("id_partidos");
        $datos = $this->partido->del_many($ids);
        $this->response($datos);
    }

    public function create_partido_post() {
        $partido = $this->post("partido");
        $datos = $this->partido->create_one($partido);
        $this->response($datos);
    }

    public function update_partido_post($id) {
        $partido = $this->post("partido");
        $datos = $this->partido->update_one($id, $partido);
        $this->response($datos);
    }

}
