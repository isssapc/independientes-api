<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/** Include path * */
set_include_path(APPPATH . 'libraries/PHPExcel-1.8/Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';

class Secciones extends MY_Controller {

    private $dir;

    public function __construct() {
        parent::__construct();

        //en el parent parent::__construct se ejecutan los operaciones de middleware
        $database = $this->middlewares['auth']->claims["db"];
        $this->dir = $this->middlewares['auth']->claims["dir"];
        $this->db->db_select($database);

        $this->load->model("seccion");
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
        $datos = $this->seccion->get_all();
        $this->response($datos);
    }

    public function get_count_get() {
        $datos = $this->seccion->get_count();
        $this->response(array('count' => $datos));
    }

    public function get_page_get($pageSize, $page) {
        $datos = $this->seccion->get_page($pageSize, $page);
        $this->response($datos);
    }

    public function get_seccion_get($id) {
        $datos = $this->seccion->get_one($id);
        $this->response($datos);
    }

    public function search_seccion_get($nombre) {
        $datos = $this->seccion->search_by_nombre($nombre);
        $this->response($datos);
    }

    public function del_seccion_post($id) {
        //$id= $this->post("id_registro");
        $count = $this->seccion->del_one($id);
        $this->response(array("count" => $count));
    }

    public function del_all_post() {
        $datos = $this->seccion->del_all();
        $this->response($datos);
    }

    public function del_seccions_post() {
        $ids = $this->post("id_seccions");
        $datos = $this->seccion->del_many($ids);
        $this->response($datos);
    }

    public function create_seccion_post() {
        $seccion = $this->post("seccion");
        $datos = $this->seccion->create_one($seccion);
        $this->response($datos);
    }

    public function update_seccion_post($id) {
        $seccion = $this->post("seccion");
        $datos = $this->seccion->update_one($id, $seccion);
        $this->response($datos);
    }

    public function upload_excel_post() {

        $path = "./public/$this->dir/secciones";

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
            $excel = $this->_excelToarray($path . '/' . $data['file_name']);
            $data = $this->seccion->create_many($excel);
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

        //la fila 1 está reservada para la cabecera
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
            //al menos debe tener valor el id_seccion
            if (isset($fila['A'])) {
                $data[] = array(
                    "id_seccion" => $fila['A'],
                    "num_electores" => $fila['B']
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
