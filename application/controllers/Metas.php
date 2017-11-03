<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Metas extends MY_Controller {

    public $empresa;

    public function __construct() {
        parent::__construct();
        //en el parent parent::__construct se ejecutan los operaciones de middleware
        $this->empresa = $this->middlewares['auth']->claims;

        //$this->load->database($this->empresa["org"]);
        $this->db->db_select($this->empresa["db"]);
        $this->load->model("meta");
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

        $datos = $this->meta->get_all();
        $this->response($datos);
    }

    public function get_meta_get($id) {
        $datos = $this->meta->get_one($id);
        $this->response($datos);
    }

    public function del_meta_post($id) {
        $count = $this->meta->del_one($id);
        $this->response(array("count" => $count));
    }

    public function create_meta_post() {
        $meta = $this->post("meta");
        $datos = $this->meta->create_one($meta);
        $this->response($datos);
    }

    public function update_meta_post() {
        $meta = $this->post("meta");
        $datos = $this->meta->update_one($meta);
        $this->response($datos);
    }

}
