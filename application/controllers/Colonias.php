<?php

defined('BASEPATH') OR exit('No direct script access allowed');

//require_once APPPATH . 'libraries/PhpSpreadsheet/IOFactory.php';
/** Include path * */
set_include_path(APPPATH . 'libraries/PHPExcel-1.8/Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';

class Colonias extends MY_Controller {

    private $dir;

    public function __construct() {
        parent::__construct();
        //en el parent parent::__construct se ejecutan los operaciones de middleware
        $database = $this->middlewares['auth']->claims["db"];
        $this->dir = $this->middlewares['auth']->claims["dir"];
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

    public function get_count_get() {
        $datos = $this->colonia->get_count();
        $this->response(array("count" => $datos));
    }

    public function get_page_get($pageSize, $page) {
        $datos = $this->colonia->get_page($pageSize, $page);
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

    public function del_all_post() {
        $datos = $this->colonia->del_all();
        $this->response($datos);
    }

    public function create_colonia_post() {
        $colonia = $this->post("colonia");
        $datos = $this->colonia->create_one($colonia);
        $this->response($datos);
    }

    public function create_colonias_post() {
        $colonias = $this->post("colonias");
        $datos = $this->colonia->create_many($colonias);
        $this->response($datos);
    }

    public function update_colonia_post($id) {
        $colonia = $this->post("colonia");
        $datos = $this->colonia->update_one($id, $colonia);
        $this->response($datos);
    }

    public function upload_excel_post() {


        $path = "./public/$this->dir/colonias";

        if (!file_exists($path)) {
            mkdir($path, 0777, TRUE);
        }

        $config['upload_path'] = $path;
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
            //$this->response(["data" => $data]);
            $excel = $this->_excelToarray($path . '/' . $data['file_name']);
            $data = $this->colonia->create_many($excel);
            $this->response($data);
        }
    }

    private function _excelToarray($filename) {
        //creamos el reader
        $nombre_archivo = $filename;

        $tipo_archivo = PHPExcel_IOFactory::identify($nombre_archivo);
        $reader = PHPExcel_IOFactory::createReader($tipo_archivo);
        $reader->setReadDataOnly(true);

        $worksheetData = $reader->listWorksheetInfo($nombre_archivo);
        $totalRows = $worksheetData[0]["totalRows"];
        $totalCols = $worksheetData[0]["totalColumns"];
        //$lastColLetter = $worksheetData[0]['lastColumnLetter'];
        $lastColLetter = 'B';

        //empieza en la fila 2, la 1 está reservada para la cabecera
        $filtro = new MyReadFilter(2, $totalRows, range('A', $lastColLetter));
        $reader->setReadFilter($filtro);

        $excel = $reader->load($nombre_archivo);

        //$data = array("rows" => $totalRows, "cols" => $totalCols, "lastLetter" => $lastColLetter); //[];
        //obtenemos los datos de la hoja activa (la primera)
        $worksheet = $excel->getActiveSheet();
        if (!isset($worksheet)) {
            $worksheet = $excel->getSheet(0);
        }

        $sheetData = $worksheet->toArray(NULL, TRUE, TRUE, TRUE);

        foreach ($sheetData as $fila) {
            if (isset($fila['A']) && isset($fila['B'])) {
                $data[] = array(
                    //"id_colonia" => $fila['A'],                    
                    "nombre" => $fila['A'],
                    "id_seccion" => $fila['B']
                );
            }
        }

        return $data;
    }

}

class MyReadFilter implements PHPExcel_Reader_IReadFilter {

    private $_startRow = 0;
    private $_endRow = 0;
    private $_columns = array();

    public function __construct($startRow, $endRow, $columns) {
        $this->_startRow = $startRow;
        $this->_endRow = $endRow;
        $this->_columns = $columns;
    }

    public function readCell($column, $row, $worksheetName = '') {
        if ($row >= $this->_startRow && $row <= $this->_endRow) {
            if (in_array($column, $this->_columns)) {
                return true;
            }
        }
        return false;
    }

}
