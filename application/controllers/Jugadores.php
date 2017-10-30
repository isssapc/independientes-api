<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Jugadores extends MY_Controller {

    public function __construct() {
        parent::__construct();

        //en el parent parent::__construct se ejecutan los operaciones de middleware
        $database = $this->middlewares['auth']->claims["db"];
        $this->db->db_select($database);

        $this->load->model("jugador");
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
        $datos = $this->jugador->get_all();
        $this->response($datos);
    }

    public function get_jugador_get($id) {
        $datos = $this->jugador->get_one($id);
        $this->response($datos);
    }

    public function search_jugador_get($nombre) {
        $datos = $this->jugador->search_by_nombre($nombre);
        $this->response($datos);
    }

    public function del_jugador_post($id) {
        $count = $this->jugador->del_one($id);
        $this->response(array("count" => $count));
    }

    public function del_jugadores_post() {
        $ids = $this->post("id_jugadores");
        $datos = $this->jugador->del_many($ids);
        $this->response($datos);
    }

    public function create_jugador_post() {
        $jugador = $this->post("jugador");
        $datos = $this->jugador->create_one($jugador);
        $this->response($datos);
    }

    public function update_jugador_post($id) {
        $jugador = $this->post("jugador");
        $datos = $this->jugador->update_one($id, $jugador);
        $this->response($datos);
    }

}
