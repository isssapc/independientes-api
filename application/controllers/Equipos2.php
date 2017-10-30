<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Equipos extends MY_Controller {

    public function __construct() {
        parent::__construct();
        //en el parent parent::__construct se ejecutan los operaciones de middleware
        $database = $this->middlewares['auth']->claims["db"];
        $this->db->db_select($database);
        
        $this->load->model("equipo");
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
        $datos = $this->equipo->get_all();
        $this->response($datos);
    }

    public function get_equipos_liga_get($id_liga) {
        $datos = $this->equipo->get_equipos_liga($id_liga);
        $this->response($datos);
    }

    public function get_jugadores_get($id_equipo) {
        $datos = $this->jugador->get_jugadores_equipo($id_equipo);
        $this->response($datos);
    }

    public function get_equipo_get($id) {
        $datos = $this->equipo->get_one($id);
        $this->response($datos);
    }

    public function get_equipo_con_jugadores_get($id) {
        $equipo = $this->equipo->get_one($id);
        $jugadores = $this->jugador->get_jugadores_equipo($id);
        $equipo["jugadores"] = $jugadores;
        $this->response($equipo);
    }

    public function search_equipo_get($nombre) {
        $datos = $this->equipo->search_by_nombre($nombre);
        $this->response($datos);
    }

    public function del_equipo_post($id) {
        $count = $this->equipo->del_one($id);
        $this->response(array("count" => $count));
    }

    public function del_equipos_post() {
        $ids = $this->post("id_equipos");
        $datos = $this->equipo->del_many($ids);
        $this->response($datos);
    }

    public function create_equipo_post() {
        $equipo = $this->post("equipo");
        $datos = $this->equipo->create_one($equipo);
        $this->response($datos);
    }

    public function update_equipo_post($id) {
        $equipo = $this->post("equipo");
        $datos = $this->equipo->update_one($id, $equipo);
        $this->response($datos);
    }

}
