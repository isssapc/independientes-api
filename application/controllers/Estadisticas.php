<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Estadisticas extends MY_Contestadisticaler {

    public function __construct() {
        parent::__construct();
        $this->load->model("estadistica");
    }

    public function index_get() {
        $datos = $this->estadistica->get_all();
        $this->response($datos);
    }

    public function get_estadistica_get($id) {
        $datos = $this->estadistica->get_one($id);
        $this->response($datos);
    }

    public function del_estadistica_post($id) {
        $datos = $this->estadistica->del_one($id);
        $this->response($datos);
    }

    public function del_estadisticas_post() {
        $ids = $this->post("id_estadisticas");
        $datos = $this->estadistica->del_many($ids);
        $this->response($datos);
    }

    public function create_estadistica_post() {
        $estadistica = $this->post("estadistica");
        $datos = $this->estadistica->create_one($estadistica);
        $this->response($datos);
    }

    public function update_estadistica_post() {
        $estadistica = $this->post("estadistica");
        $datos = $this->estadistica->update_one($estadistica);
        $this->response($datos);
    }

}
