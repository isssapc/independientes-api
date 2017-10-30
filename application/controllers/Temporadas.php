<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Temporadas extends MY_Controller {

    public $empresa;

    public function __construct() {
        parent::__construct();
        //en el parent parent::__construct se ejecutan los operaciones de middleware
        $this->empresa = $this->middlewares['auth']->claims;

        //$this->load->database($this->empresa["org"]);
        $this->db->db_select($this->empresa["db"]);
        $this->load->model("temporada");
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
        
        $datos = $this->temporada->get_all();
        //$datos["empresa"] = $this->empresa;
        //$datos["default"] = $this->config->item("index_page");
        $this->response($datos);
    }

    public function get_temporada_get($id) {
        $datos = $this->temporada->get_one($id);
        $this->response($datos);
    }

    public function search_temporada_get($nombre) {
        $datos = $this->temporada->search_by_nombre($nombre);
        $this->response($datos);
    }

    public function del_temporada_post($id) {
        $count = $this->temporada->del_one($id);
        $this->response(array("count" => $count));
    }

    public function del_temporadas_post() {
        $ids = $this->post("id_temporadas");
        $datos = $this->temporada->del_many($ids);
        $this->response($datos);
    }

    public function create_temporada_post() {
        $temporada = $this->post("temporada");
        $datos = $this->temporada->create_one($temporada);
        $this->response($datos);
    }

    public function update_temporada_post($id) {
        $temporada = $this->post("temporada");
        $datos = $this->temporada->update_one($id, $temporada);
        $this->response($datos);
    }

}
