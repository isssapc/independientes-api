<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Registros extends MY_Controller {

    public function __construct() {
        parent::__construct();

        //en el parent parent::__construct se ejecutan los operaciones de middleware
        $database = $this->middlewares['auth']->claims["db"];
        $this->db->db_select($database);

        $this->load->model("registro");
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
        $datos = $this->registro->get_all();
        $this->response($datos);
    }

    public function get_registro_get($id) {
        $datos = $this->registro->get_one($id);
        $this->response($datos);
    }

    public function search_registro_get($nombre) {
        $datos = $this->registro->search_by_nombre($nombre);
        $this->response($datos);
    }

    public function del_registro_post($id) {
        $count = $this->registro->del_one($id);
        $this->response(array("count" => $count));
    }

    public function del_registros_post() {
        $ids = $this->post("id_registros");
        $datos = $this->registro->del_many($ids);
        $this->response($datos);
    }

    public function create_registro_post() {
        $registro = $this->post("registro");
        $datos = $this->registro->create_one($registro);
        $this->response($datos);
    }

    public function update_registro_post($id) {
        $registro = $this->post("registro");
        $datos = $this->registro->update_one($id, $registro);
        $this->response($datos);
    }
    
        
    public function upload_excel_post() {
        $config['upload_path'] = './public/registros';
        $config['allowed_types'] = 'xls|xlsx';
        $config['max_size'] = 4096; //4MB
        $config['overwrite'] = FALSE;
        $config['file_ext_tolower'] = TRUE;
        $config['remove_spaces'] = TRUE;


        $this->load->library('upload', $config);

        $file = 'file';
        if (!$this->upload->do_upload($file)) {
            $error = $this->upload->error_msg;
            $this->response(["error" => $error], REST_Controller::HTTP_BAD_REQUEST);
        } else {
            $data = $this->upload->data();
            $this->response(["data" => $data]);
            //$this->excelToarray($data['file_name']);
        }
    }

}
