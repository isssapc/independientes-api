<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Lotes extends MY_Controller {

    public function __construct() {
        parent::__construct();
        //en el parent parent::__construct se ejecutan los operaciones de middleware
        $database = $this->middlewares['auth']->claims["db"];
        $this->db->db_select($database);

        $this->load->model("lote");
   
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
        $datos = $this->lote->get_all();
        $this->response($datos);
    } 

    public function get_lote_get($id) {
        $lote = $this->lote->get_one($id);
        $jornadas = $this->jornada->get_jornadas_lote($id);
        $equipos = $this->equipo->get_equipos_lote($id);
        $this->response(["lote" => $lote, "jornadas" => $jornadas, "equipos" => $equipos]);
    }

    public function search_lote_get($nombre) {
        $datos = $this->lote->search_by_nombre($nombre);
        $this->response($datos);
    }

    public function del_lote_post($id) {
        $count = $this->lote->del_one($id);
        $this->response(array("count" => $count));
    }

    public function del_lotes_post() {
        $ids = $this->post("id_lotes");
        $datos = $this->lote->del_many($ids);
        $this->response($datos);
    }

    public function create_lote_post() {
        $lote = $this->post("lote");
        $datos = $this->lote->create_one($lote);
        $this->response($datos);
    }

    public function update_lote_post($id) {
        $lote = $this->post("lote");
        $datos = $this->lote->update_one($id, $lote);
        $this->response($datos);
    }

}
