<?php

defined('BASEPATH') OR exit('No direct script access allowed');

define('FPDF_FONTPATH', APPPATH . 'libraries/fpdf181/font/');
require_once APPPATH . 'libraries/fpdf181/fpdf.php';

/** Include path * */
set_include_path(APPPATH . 'libraries/PHPExcel-1.8/Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';

class Registros extends MY_Controller {

    private $dir;

    public function __construct() {
        parent::__construct();

        //en el parent parent::__construct se ejecutan los operaciones de middleware
        $database = $this->middlewares['auth']->claims["db"];
        $this->dir = $this->middlewares['auth']->claims["dir"];
        $this->db->db_select($database);

        $this->load->model("registro");
        $this->load->model("lote");
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
        return array('auth|except:exportar_excel_get');
    }

    public function index_get() {
        $datos = $this->registro->get_all();
        $this->response($datos);
    }

    public function get_resumen_get() {
        $metas = $this->meta->get_one(1);
        $num_firmas = $this->registro->get_count();
        $num_firmas_necesarias = $metas["padron"] * ($metas["meta_padron"] / 100);
        $pct_firmas = $num_firmas / $metas["padron"];
        $num_firmas_restantes = $num_firmas_necesarias - $num_firmas;
        $pct_pendiente = ($metas["meta_padron"] / 100) - $pct_firmas;



        $resumen = array(
            "padron" => $metas["padron"],
            "num_secciones" => $metas["num_secciones"],
            "meta_padron" => $metas["meta_padron"],
            "meta_secciones" => $metas["meta_secciones"],
            "num_firmas" => $num_firmas,
            "num_firmas_necesarias" => $num_firmas_necesarias,
            "num_firmas_restantes" => $num_firmas_restantes,
            "pct_firmas" => $pct_firmas,
            "pct_pendiente" => $pct_pendiente,
        );

        $this->response($resumen);
    }

    public function group_by_seccion_get() {
        $datos = $this->registro->group_by_seccion();
        $this->response($datos);
    }

    public function get_count_get() {
        $datos = $this->registro->get_count();
        $this->response(array("count" => $datos));
    }

    public function get_count_error_get() {
        $datos = $this->registro->get_count_error();
        $this->response(array("count" => $datos));
    }

    public function get_count_error_lote_get($id_lote) {
        $datos = $this->registro->get_count_error_lote($id_lote);
        $this->response($datos);
    }

    public function get_count_validos_lote_get($id_lote) {
        $datos = $this->registro->get_count_validos_lote($id_lote);
        $this->response($datos);
    }

    public function get_page_get($pageSize, $page) {
        $datos = $this->registro->get_page($pageSize, $page);
        $this->response($datos);
    }

    public function get_page_error_get($pageSize, $page) {
        $datos = $this->registro->get_page_error($pageSize, $page);
        $this->response($datos);
    }

    public function get_page_error_lote_get($id_lote, $pageSize, $page) {
        $datos = $this->registro->get_page_error_lote($id_lote, $pageSize, $page);
        $this->response($datos);
    }

    public function get_page_validos_lote_get($id_lote, $pageSize, $page) {
        $datos = $this->registro->get_page_validos_lote($id_lote, $pageSize, $page);
        $this->response($datos);
    }

    public function get_registro_get($id) {
        $datos = $this->registro->get_one($id);
        $this->response($datos);
    }

    public function buscar_registros_post() {
        $texto = $this->post("texto");
        $tipo = $this->post("tipo_busqueda");
        $pageSize = $this->post("page_size");
        $page = $this->post("page");

        $datos = $this->registro->search_by($texto, $tipo, $page, $pageSize);
        $this->response($datos);
    }

    public function del_registro_post($id) {
        //$id= $this->post("id_registro");
        $count = $this->registro->del_one($id);
        $this->response(array("count" => $count));
    }

    public function del_registro_error_post($id) {

        $registro = $this->registro->get_one_error($id);
        $count = $this->registro->del_one_error($id);

        //actualizamos el lote
        $count_error = $this->registro->get_count_error_lote($registro['id_lote']);
        $this->lote->update_one($registro['id_lote'], array('num_registros_errores' => $count_error));

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

    public function update_registro_error_post($id) {
        $registro = $this->post("registro");
        $datos = $this->registro->update_one_error($id, $registro);

        //actualizamos el lote si se corrigió el error
        if ($datos['valido']) {
            $count_error = $this->registro->get_count_error_lote($registro['id_lote']);
            $count_validos = $this->registro->get_count_validos_lote($registro['id_lote']);
            $this->lote->update_one($registro['id_lote'], array('num_registros_validos' => $count_validos, 'num_registros_errores' => $count_error));
        }

        $this->response($datos);
    }

    public function upload_excel_post() {

        $responsable = $this->post("responsable");
        $nombre_lote = $this->post("lote");
        $lote = ["responsable" => $responsable, "nombre" => $nombre_lote];

        $lote = $this->lote->create_one($lote);

        $path = "./public/$this->dir/registros";

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
            //leemos los datos del array
            // en data['file_name'] solo es el nombre sin path
            // en data['file_path´] solo el path absoluto
            // en data['full_path'] el path absoluto y filename
            $excel = $this->_excelToarray($path . "/" . $data['file_name']);
            //insertamos en la tabla de validos o errores
            $datos_lote = $this->registro->create_many($lote["id_lote"], $excel);
            $datos_lote['path'] = "api/public/$this->dir/registros";
            $datos_lote['filename'] = $data['file_name'];
            //actualizamos el lote con el numero de registros
            $lote = $this->lote->update_one($lote["id_lote"], $datos_lote);
            //devolvemos el lote con el conteo de registros
            $this->response($lote);

            //$this->response($lote);
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
        $lastColLetter = 'H';

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

        /*
          foreach ($sheetData as $fila) {
          $data[] = array(
          "folio" => $fila['A'],
          "clave_elector" => $fila['B'],
          "ocr" => $fila['C'],
          "ap_paterno" => $fila['D'],
          "ap_materno" => $fila['E'],
          "nombre" => $fila['F'],
          //"cel" => $fila['G'],
          "id_seccion" => $fila['G']
          );
          } */

        //comenzamos por la fila 2 porque en la 1 está la cabecera
        for ($i = 2; $i <= count($sheetData); $i++) {
            $fila = $sheetData[$i];
            $data[] = array(
                "folio" => $fila['A'],
                "clave_elector" => $fila['B'],
                "ocr" => $fila['C'],
                "ap_paterno" => $fila['D'],
                "ap_materno" => $fila['E'],
                "nombre" => $fila['F'],
                "id_seccion" => $fila['G'],
                "fila" => $i
            );
        }



        return $data;
    }

    public function exportar_excel_get() {
        $registros = $this->registro->get_all_to_export();
        $excel = new PHPExcel();
        $workSheet = new PHPExcel_Worksheet($excel, "Firmas");
        $excel->addSheet($workSheet, 0);

        //$encabezados = ["Folio", "Clave Elector", "OCR", "Apellido Paterno", "Apellido Materno", "Nombre", "Sección", "Lote"];
        //$workSheet->fromArray($encabezados, NULL, "A1");
        //$colEncabezados = array_chunk($encabezados, 1);
        //$workSheet->fromArray($colEncabezados, NULL, "A1");

        $workSheet->fromArray($registros, NULL, "A2");

        $writer = PHPExcel_IOFactory::createWriter($excel, "Excel2007");

        // redirect output to client browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="firmas.xlsx"');
        header('Cache-Control: max-age=0');


        $writer->save('php://output');


        //$writer->save("./public/prueba.xlsx");
//        
//        
//        $content=file_get_contents("./public/prueba.xlsx");
        //$this->load->helper('download');
//        force_download("prueba.xlsx",$content, true);
        //force_download("./public/prueba.xlsx", NULL, true);
    }

    public function pdf_errores_lote_get($id_lote) {

        $datos = $this->registro->get_error_lote($id_lote);
        $lote = $this->lote->get_one($id_lote);

        $frame = 0;
        $right = 0;
        $ln = 1;
        $hl = 6;

        $pdf = new FPDF("L", "mm", "Letter");
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 14);
        $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', 'ERRORES EN LOTE DE CAPTURA'), $frame, $ln, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', 'Lote: ' . $lote['nombre']), $frame, $ln, 'L');
        $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', 'Responsable: ' . $lote['responsable']), $frame, $ln, 'L');
        $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', 'Fecha importación: ' . $lote['fecha']), $frame, $ln, 'L');
        $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', 'Archivo: ' . $lote['filename']), $frame, $ln, 'L');
        $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', 'Núm. Errores: ' . $lote['num_registros_errores']), $frame, $ln, 'L');
        $pdf->Ln(4);

        $pdf->SetFont('Arial', 'B', 8);
        $cframe = 1;
        $pdf->Cell(10, $hl, "Folio", $cframe, $right, 'C');
        $pdf->Cell(35, $hl, "Elector", $cframe, $right, 'C');
        $pdf->Cell(25, $hl, "OCR", $cframe, $right, 'C');
        $pdf->Cell(30, $hl, "A. Paterno", $cframe, $right, 'C');
        $pdf->Cell(30, $hl, "A. Materno", $cframe, $right, 'C');
        $pdf->Cell(35, $hl, "Nombre", $cframe, $right, 'C');
        $pdf->Cell(15, $hl, iconv('utf-8', 'iso-8859-1', "Sección"), $cframe, $right, 'C');
        $pdf->Cell(0, $hl, "Error", $cframe, $ln, 'C');

        $pdf->SetFont('');

        foreach ($datos as $registro) {

            $errores = explode(",", $registro['errores']);
            $strErrores = "";
            foreach ($errores as $error) {
                $strErrores = $strErrores . '- ' . $error . "\n";
            }

            $pdf->Cell(10, $hl, iconv('utf-8', 'iso-8859-1', $registro['folio']), $frame, $right, 'L');
            $pdf->Cell(35, $hl, iconv('utf-8', 'iso-8859-1', $registro['clave_elector']), $frame, $right, 'L');
            $pdf->Cell(25, $hl, iconv('utf-8', 'iso-8859-1', $registro['ocr']), $frame, $right, 'L');
            $pdf->Cell(30, $hl, iconv('utf-8', 'iso-8859-1', $registro['ap_paterno']), $frame, $right, 'L');
            $pdf->Cell(30, $hl, iconv('utf-8', 'iso-8859-1', $registro['ap_materno']), $frame, $right, 'L');
            $pdf->Cell(35, $hl, iconv('utf-8', 'iso-8859-1', $registro['nombre']), $frame, $right, 'L');
            $pdf->Cell(15, $hl, iconv('utf-8', 'iso-8859-1', $registro['id_seccion']), $frame, $right, 'L');
            $pdf->MultiCell(0, $hl, iconv('utf-8', 'iso-8859-1', $strErrores), $frame, 'L');
        }




        $pdf->Output("ReporteErroresLote.pdf", "I");
    }

    public function pdf_validos_lote_get($id_lote) {

        $datos = $this->registro->get_validos_lote($id_lote);
        $lote = $this->lote->get_one($id_lote);

        $frame = 0;
        $right = 0;
        $ln = 1;
        $hl = 6;

        $pdf = new FPDF("L", "mm", "Letter");
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 14);
        $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', 'REGISTROS VÁLIDOS EN LOTE DE CAPTURA'), $frame, $ln, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', 'Lote: ' . $lote['nombre']), $frame, $ln, 'L');
        $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', 'Responsable: ' . $lote['responsable']), $frame, $ln, 'L');
        $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', 'Fecha importación: ' . $lote['fecha']), $frame, $ln, 'L');
        $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', 'Archivo: ' . $lote['filename']), $frame, $ln, 'L');
        $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', 'Núm. Registros Válidos: ' . $lote['num_registros_validos']), $frame, $ln, 'L');
        $pdf->Ln(4);

        $pdf->SetFont('Arial', 'B', 8);
        $cframe = 1;
        $pdf->Cell(15, $hl, "Folio", $cframe, $right, 'C');
        $pdf->Cell(35, $hl, "Elector", $cframe, $right, 'C');
        $pdf->Cell(25, $hl, "OCR", $cframe, $right, 'C');
        $pdf->Cell(15, $hl, iconv('utf-8', 'iso-8859-1', "Sección"), $cframe, $right, 'C');
        $pdf->Cell(50, $hl, "A. Paterno", $cframe, $right, 'C');
        $pdf->Cell(50, $hl, "A. Materno", $cframe, $right, 'C');
        $pdf->Cell(0, $hl, "Nombre", $cframe, $ln, 'C');

        $pdf->SetFont('');
        foreach ($datos as $registro) {
            $pdf->Cell(15, $hl, iconv('utf-8', 'iso-8859-1', $registro['folio']), $frame, $right, 'L');
            $pdf->Cell(35, $hl, iconv('utf-8', 'iso-8859-1', $registro['clave_elector']), $frame, $right, 'L');
            $pdf->Cell(25, $hl, iconv('utf-8', 'iso-8859-1', $registro['ocr']), $frame, $right, 'L');
            $pdf->Cell(15, $hl, iconv('utf-8', 'iso-8859-1', $registro['id_seccion']), $frame, $right, 'L');
            $pdf->Cell(50, $hl, iconv('utf-8', 'iso-8859-1', $registro['ap_paterno']), $frame, $right, 'L');
            $pdf->Cell(50, $hl, iconv('utf-8', 'iso-8859-1', $registro['ap_materno']), $frame, $right, 'L');
            $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', $registro['nombre']), $frame, $ln, 'L');
        }

        $pdf->Output("ReporteRegistrosValidadosLote.pdf", "I");
    }

    public function pdf_validos_get() {

        $datos = $this->registro->get_all();


        $frame = 0;
        $right = 0;
        $ln = 1;
        $hl = 6;

        $pdf = new FPDF("L", "mm", "Letter");
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 14);
        $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', 'REGISTROS VÁLIDOS'), $frame, $ln, 'C');
        $pdf->Ln(4);

        $pdf->SetFont('Arial', 'B', 8);
        $cframe = 1;
        $pdf->Cell(15, $hl, "Lote", $cframe, $right, 'C');
        $pdf->Cell(15, $hl, "Folio", $cframe, $right, 'C');
        $pdf->Cell(35, $hl, "Elector", $cframe, $right, 'C');
        $pdf->Cell(25, $hl, "OCR", $cframe, $right, 'C');
        $pdf->Cell(15, $hl, iconv('utf-8', 'iso-8859-1', "Sección"), $cframe, $right, 'C');
        $pdf->Cell(50, $hl, "A. Paterno", $cframe, $right, 'C');
        $pdf->Cell(50, $hl, "A. Materno", $cframe, $right, 'C');
        $pdf->Cell(0, $hl, "Nombre", $cframe, $ln, 'C');

        $pdf->SetFont('');

        foreach ($datos as $registro) {
            $pdf->Cell(15, $hl, iconv('utf-8', 'iso-8859-1', $registro['lote']), $frame, $right, 'L');
            $pdf->Cell(15, $hl, iconv('utf-8', 'iso-8859-1', $registro['folio']), $frame, $right, 'L');
            $pdf->Cell(35, $hl, iconv('utf-8', 'iso-8859-1', $registro['clave_elector']), $frame, $right, 'L');
            $pdf->Cell(25, $hl, iconv('utf-8', 'iso-8859-1', $registro['ocr']), $frame, $right, 'L');
            $pdf->Cell(15, $hl, iconv('utf-8', 'iso-8859-1', $registro['id_seccion']), $frame, $right, 'L');
            $pdf->Cell(50, $hl, iconv('utf-8', 'iso-8859-1', $registro['ap_paterno']), $frame, $right, 'L');
            $pdf->Cell(50, $hl, iconv('utf-8', 'iso-8859-1', $registro['ap_materno']), $frame, $right, 'L');
            $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', $registro['nombre']), $frame, $ln, 'L');
        }

        $pdf->Output("ReporteRegistrosValidados.pdf", "I");
    }

    public function pdf_errores_get() {

        $datos = $this->registro->get_all_error();


        $frame = 0;
        $right = 0;
        $ln = 1;
        $hl = 6;

        $pdf = new FPDF("L", "mm", "Letter");
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 14);
        $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', 'ERRORES DE CAPTURA'), $frame, $ln, 'C');
        $pdf->Ln(4);

        $pdf->SetFont('Arial', 'B', 8);
        $cframe = 1;
        $pdf->Cell(15, $hl, "Lote", $cframe, $right, 'C');
        $pdf->Cell(10, $hl, "Folio", $cframe, $right, 'C');
        $pdf->Cell(35, $hl, "Elector", $cframe, $right, 'C');
        $pdf->Cell(25, $hl, "OCR", $cframe, $right, 'C');
        $pdf->Cell(30, $hl, "A. Paterno", $cframe, $right, 'C');
        $pdf->Cell(30, $hl, "A. Materno", $cframe, $right, 'C');
        $pdf->Cell(35, $hl, "Nombre", $cframe, $right, 'C');
        $pdf->Cell(15, $hl, iconv('utf-8', 'iso-8859-1', "Sección"), $cframe, $right, 'C');
        $pdf->Cell(0, $hl, "Error", $cframe, $ln, 'C');

        $pdf->SetFont('');

        foreach ($datos as $registro) {

            $errores = explode(",", $registro['errores']);
            $strErrores = "";
            foreach ($errores as $error) {
                $strErrores = $strErrores . '- ' . $error . "\n";
            }
            $pdf->Cell(15, $hl, iconv('utf-8', 'iso-8859-1', $registro['lote']), $frame, $right, 'L');
            $pdf->Cell(10, $hl, iconv('utf-8', 'iso-8859-1', $registro['folio']), $frame, $right, 'L');
            $pdf->Cell(35, $hl, iconv('utf-8', 'iso-8859-1', $registro['clave_elector']), $frame, $right, 'L');
            $pdf->Cell(25, $hl, iconv('utf-8', 'iso-8859-1', $registro['ocr']), $frame, $right, 'L');
            $pdf->Cell(30, $hl, iconv('utf-8', 'iso-8859-1', $registro['ap_paterno']), $frame, $right, 'L');
            $pdf->Cell(30, $hl, iconv('utf-8', 'iso-8859-1', $registro['ap_materno']), $frame, $right, 'L');
            $pdf->Cell(35, $hl, iconv('utf-8', 'iso-8859-1', $registro['nombre']), $frame, $right, 'L');
            $pdf->Cell(15, $hl, iconv('utf-8', 'iso-8859-1', $registro['id_seccion']), $frame, $right, 'L');
            $pdf->MultiCell(0, $hl, iconv('utf-8', 'iso-8859-1', $strErrores), $frame, 'L');
        }

        $pdf->Output("ReporteErrores.pdf", "I");
    }

    public function pdf_avance_seccion_get() {

        $registros = $this->registro->group_by_seccion();

        $frame = 0;
        $right = 0;
        $ln = 1;
        $hl = 6;

        $pdf = new FPDF("P", "mm", "Letter");
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 14);
        $pdf->Cell(0, $hl, iconv('utf-8', 'iso-8859-1', 'AVANCE POR SECCIÓN'), $frame, $ln, 'C');
        $pdf->Ln(4);

        $pdf->SetFont('Arial', 'B', 8);
        $cframe = 1;
        $pdf->Cell(50, $hl, '', 0, $right, 'C');
        $pdf->Cell(20, $hl, iconv('utf-8', 'iso-8859-1', "Sección"), $cframe, $right, 'C');
        $pdf->Cell(30, $hl, iconv('utf-8', 'iso-8859-1', "Núm. Electores"), $cframe, $right, 'C');
        $pdf->Cell(25, $hl, iconv('utf-8', 'iso-8859-1', "Núm. Firmas"), $cframe, $right, 'C');
        $pdf->Cell(20, $hl, "Porcentaje", $cframe, $ln, 'C');

        $pdf->SetFont('');

        foreach ($registros as $registro) {
            $pdf->Cell(50, $hl, '', $frame, $right, 'C');
            $pdf->Cell(20, $hl, iconv('utf-8', 'iso-8859-1', $registro['id_seccion']), $frame, $right, 'C');
            $pdf->Cell(30, $hl, iconv('utf-8', 'iso-8859-1', number_format($registro['num_electores'])), $frame, $right, 'C');
            $pdf->Cell(25, $hl, iconv('utf-8', 'iso-8859-1', number_format($registro['num_registros'])), $frame, $right, 'C');
            $pdf->Cell(20, $hl, iconv('utf-8', 'iso-8859-1', number_format($registro['porcentaje'] * 100, 2)) . ' %', $frame, $ln, 'L');
        }

        $pdf->Output("ReporteAvancePorSeccion.pdf", "I");
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
