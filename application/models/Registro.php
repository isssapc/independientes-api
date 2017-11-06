<?php

class Registro extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all() {

        $sql = "SELECT *
                FROM registro LIMIT 100";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_all_to_export() {

        $sql = "SELECT 
                folio,
                SUBSTR(clave_elector, 1,1) AS c1,
                SUBSTR(clave_elector, 2,1) AS c2,
                SUBSTR(clave_elector, 3,1) AS c3,
                SUBSTR(clave_elector, 4,1) AS c4,
                SUBSTR(clave_elector, 5,1) AS c5,
                SUBSTR(clave_elector, 6,1) AS c6,
                SUBSTR(clave_elector, 7,1) AS c7,
                SUBSTR(clave_elector, 8,1) AS c8,
                SUBSTR(clave_elector, 9,1) AS c9,
                SUBSTR(clave_elector, 10,1) AS c10,
                SUBSTR(clave_elector, 11,1) AS c11,
                SUBSTR(clave_elector, 12,1) AS c12,
                SUBSTR(clave_elector, 13,1) AS c13,
                SUBSTR(clave_elector, 14,1) AS c14,
                SUBSTR(clave_elector, 15,1) AS c15,
                SUBSTR(clave_elector, 16,1) AS c16,
                SUBSTR(clave_elector, 17,1) AS c17,
                SUBSTR(clave_elector, 18,1) AS c18,
                SUBSTR(ocr, 1,1) AS o1,
                SUBSTR(ocr, 2,1) AS o2,
                SUBSTR(ocr, 3,1) AS o3,
                SUBSTR(ocr, 4,1) AS o4,
                SUBSTR(ocr, 5,1) AS o5,
                SUBSTR(ocr, 6,1) AS o6,
                SUBSTR(ocr, 7,1) AS o7,
                SUBSTR(ocr, 8,1) AS o8,
                SUBSTR(ocr, 9,1) AS o9,
                SUBSTR(ocr, 10,1) AS o10,
                SUBSTR(ocr, 11,1) AS o11,
                SUBSTR(ocr, 12,1) AS o12,
                SUBSTR(ocr, 13,1) AS o13,
                ap_paterno,
                ap_materno,
                nombre,
                SUBSTR(id_seccion, 1,1) AS s1,
                SUBSTR(id_seccion, 2,1) AS s2,
                SUBSTR(id_seccion, 3,1) AS s3,
                SUBSTR(id_seccion, 4,1) AS s4
                FROM registro LIMIT 5000";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function group_by_seccion() {

        $sql = "SELECT 
                r.id_seccion, 
                s.num_electores, 
                count(*) AS num_registros, 
                (count(*)/s.num_electores) AS porcentaje
                FROM registro r
                JOIN seccion s ON s.id_seccion= r.id_seccion
                GROUP BY r.id_seccion
                ORDER BY r.id_seccion;";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_count() {

        return $this->db->count_all('registro');
    }

    public function get_count_error() {

        return $this->db->count_all('registro_error');
    }

    public function get_count_error_lote($id_lote) {

        $sql = "SELECT COUNT(*) AS count FROM registro_error WHERE id_lote=$id_lote";
        $query = $this->db->query($sql);
        $row = $query->row();
        return $row->count;
    }

    public function get_count_validos_lote($id_lote) {

        $sql = "SELECT COUNT(*) AS count FROM registro WHERE id_lote=$id_lote";
        $query = $this->db->query($sql);
        $row = $query->row();
        return $row->count;
    }

    public function get_page($pageSize, $page) {

        $pageSize = intval($pageSize);
        $page = intval($page);
        $offset = $pageSize * $page;

        $sql = "SELECT *
                FROM registro LIMIT $offset,$pageSize";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_page_error($pageSize, $page) {

        $pageSize = intval($pageSize);
        $page = intval($page);
        $offset = $pageSize * $page;

        $sql = "SELECT *
                FROM registro_error LIMIT $offset,$pageSize";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_page_error_lote($id_lote, $pageSize, $page) {

        $pageSize = intval($pageSize);
        $page = intval($page);
        $offset = $pageSize * $page;

        $sql = "SELECT *
                FROM registro_error 
                WHERE id_lote= $id_lote LIMIT $offset,$pageSize ";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_page_validos_lote($id_lote, $pageSize, $page) {

        $pageSize = intval($pageSize);
        $page = intval($page);
        $offset = $pageSize * $page;

        $sql = "SELECT *
                FROM registro 
                WHERE id_lote= $id_lote LIMIT $offset,$pageSize ";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_one($id) {

        $sql = "SELECT r.*
                FROM registro r
                WHERE r.id_registro= $id LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function get_one_error($id) {

        $sql = "SELECT r.*
                FROM registro_error r
                WHERE r.id_registro= $id LIMIT 1";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function search_by($texto, $tipo, $page, $pageSize) {
//        $sql = "SELECT *
//                FROM registro r 
//                WHERE r.nombre like '%$nombre%'";

        $pageSize = intval($pageSize);
        $page = intval($page);
        $offset = $pageSize * $page;
        $count = 0;


        $this->db->like($tipo, $texto);
        $this->db->from("registro");
        $count = $this->db->count_all_results();


        $this->db->like($tipo, $texto);
        $this->db->limit($pageSize, $offset);
        $query = $this->db->get("registro");
        $registros = $query->result_array();

        //$query = $this->db->query($sql);
        return array("registros" => $registros, "count" => $count, "page" => $page, "pageSize" => $pageSize, "offset" => $offset);
    }

    public function del_one($id) {

        $this->db->where('id_registro', $id);
        $this->db->delete('registro');
        $count = $this->db->affected_rows();
        return $count;
    }

    public function del_one_error($id) {

        $this->db->where('id_registro', $id);
        $this->db->delete('registro_error');
        $count = $this->db->affected_rows();
        return $count;
    }

    /*
     * 
     * TODO
     */

    public function del_many($ids) {

        $sql = "SELECT t.*
                FROM registro r
                WHERE r.id_registro= $ids";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function create_one($registro) {

        $this->db->insert('registro', $registro);
        $id_registro = $this->db->insert_id();

        $registro = $this->get_one($id_registro);
        return $registro;
    }

    public function create_one_error($registro) {

        $this->db->insert('registro_error', $registro);
        $id_registro = $this->db->insert_id();

        $registro = $this->get_one_error($id_registro);
        return $registro;
    }

    public function create_many($id_lote, $registros) {

        //$this->db->insert_batch('registro', $registros);
        //$count = $this->db->affected_rows();
        //return array("count" => $count);
        //$id_colonia = $this->db->insert_id();
        //$colonia = $this->get_one($id_colonia);
        //return $colonia;

        $count = 0;
        $count_errores = 0;
        for ($i = 0; $i < count($registros); $i++) {
            $registros[$i]["id_lote"] = $id_lote;
            $error = $this->tieneErrores($registros[$i]);
            if ($error === false) {
                $this->db->insert('registro', $registros[$i]);
                $count += $this->db->affected_rows();
            } else {
                $registros[$i]["errores"] = implode(",", $error);
                $this->db->insert('registro_error', $registros[$i]);
                $count_errores += $this->db->affected_rows();
            }
        }

        return array("num_registros_validos" => $count, "num_registros_errores" => $count_errores);
    }

    public function update_one($id, $props) {

        $where = "id_registro = $id";
        $sql = $this->db->update_string('registro', $props, $where);
        $this->db->query($sql);

        $registro = $this->get_one($id);
        return $registro;
    }

    public function update_one_error($id, $props) {

        $where = "id_registro = $id";
        $sql = $this->db->update_string('registro_error', $props, $where);
        $this->db->query($sql);

        $registro = $this->get_one_error($id);
        return $registro;
    }

    private function tieneErrores($registro) {
        $error = false;
        if (!isset($registro["clave_elector"]) || empty($registro["clave_elector"])) {
            $error[] = "Clave de Elector vacía";
        } else {
            if (strlen(trim($registro["clave_elector"])) != 18) {
                $error[] = "La clave de Elector tiene una longitud distinta a 18 caracteres";
            }
        }


        if (!isset($registro["ocr"]) || empty($registro["ocr"])) {
            $error[] = "Clave OCR vacía";
        } else {
            if (strlen(trim($registro["ocr"])) != 13) {
                $error[] = "La clave OCR tiene una longitud distinta a 13 caracteres";
            }
        }




        if (!isset($registro["id_seccion"]) || empty($registro["id_seccion"])) {
            $error[] = "Sección vacía";
        } else {
            if (isset($registro["ocr"]) && !empty($registro["ocr"]) && strlen($registro["ocr"]) > 4 && substr($registro["ocr"], 0, 4) != $registro["id_seccion"]) {
                $error[] = "Sección y OCR no corresponden";
            }
        }


        if (!isset($registro["folio"]) || empty($registro["folio"])) {
            $error[] = "Folio vacío";
        }



        //implode(",", $error);
        return $error;
    }

}
