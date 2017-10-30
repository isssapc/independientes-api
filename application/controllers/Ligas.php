<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ligas extends MY_Controller {

    public function __construct() {
        parent::__construct();
        //en el parent parent::__construct se ejecutan los operaciones de middleware
        $database = $this->middlewares['auth']->claims["db"];
        $this->db->db_select($database);

        $this->load->model("liga");
        $this->load->model("jornada");
        $this->load->model("equipo");
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
        $datos = $this->liga->get_all();
        $this->response($datos);
    }

    public function get_ligas_temporada_get($id_temporada) {
        $datos = $this->liga->get_ligas_temporada($id_temporada);
        $this->response($datos);
    }

    public function get_liga_get($id) {
        $liga = $this->liga->get_one($id);
        $jornadas = $this->jornada->get_jornadas_liga($id);
        $equipos = $this->equipo->get_equipos_liga($id);
        $this->response(["liga" => $liga, "jornadas" => $jornadas, "equipos" => $equipos]);
    }

    public function search_liga_get($nombre) {
        $datos = $this->liga->search_by_nombre($nombre);
        $this->response($datos);
    }

    public function del_liga_post($id) {
        $count = $this->liga->del_one($id);
        $this->response(array("count" => $count));
    }

    public function del_ligas_post() {
        $ids = $this->post("id_ligas");
        $datos = $this->liga->del_many($ids);
        $this->response($datos);
    }

    public function create_liga_post() {
        $liga = $this->post("liga");
        $datos = $this->liga->create_one($liga);
        $this->response($datos);
    }

    public function update_liga_post($id) {
        $liga = $this->post("liga");
        $datos = $this->liga->update_one($id, $liga);
        $this->response($datos);
    }

}
