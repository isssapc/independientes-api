<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Colonias extends MY_Controller {

    public function __construct() {
        parent::__construct();
        //en el parent parent::__construct se ejecutan los operaciones de middleware
        $database = $this->middlewares['auth']->claims["db"];
        $this->db->db_select($database);
        
        $this->load->model("colonia");
        
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
        $datos = $this->colonia->get_all();
        $this->response($datos);
    }

    public function get_colonia_get($id) {
        $datos = $this->colonia->get_one($id);
        $this->response($datos);
    }
 

    public function search_colonia_get($nombre) {
        $datos = $this->colonia->search_by_nombre($nombre);
        $this->response($datos);
    }

    public function del_colonia_post($id) {
        $count = $this->colonia->del_one($id);
        $this->response(array("count" => $count));
    }

    public function del_colonias_post() {
        $ids = $this->post("id_colonias");
        $datos = $this->colonia->del_many($ids);
        $this->response($datos);
    }

    public function create_colonia_post() {
        $colonia = $this->post("colonia");
        $datos = $this->colonia->create_one($colonia);
        $this->response($datos);
    }

    public function update_colonia_post($id) {
        $colonia = $this->post("colonia");
        $datos = $this->colonia->update_one($id, $colonia);
        $this->response($datos);
    }

}
