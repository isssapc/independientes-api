<?php

require(APPPATH . 'libraries/jose/JWT.php');
require(APPPATH . 'libraries/jose/JWS.php');
require(APPPATH . 'libraries/jose/URLSafeBase64.php');

class Auth extends MY_Controller {

    public function __construct() {
        parent::__construct();

        //$this->load->database();

        $this->load->model('usuario');
    }

    public function _create_token($user) {

        /*         * *
         * sub => Subject
         * iat => IssuedAt
         * exp => Expiration
         * iss => Issuer
         * aud => Audience
         * 
         */

        $payload = [
            "sub" => $user['id_usuario'],
            "rol" => $user['rol'],
            "db" => $user["db"], //esta sería la base de datos
            "group"=> $user["empresa"], //el group en el archivo de configuracion
            //"tipo" => $user['tipo'],
            "iat" => time(),
            "exp" => time() + (60 * 60 * 4) //1 hora * 4
        ];
        $jwt = new JOSE_JWT($payload);
        $jws = $jwt->sign($this->config->item('token_secret'));

        return $jws->toString();
    }

    public function login_post() {
        $usuario = $this->post("usuario");


        $user = $this->usuario->get_by_email($usuario["email"]);

        if (!isset($user)) {
            //REST_Controller::HTTP_UNAUTHORIZED
            $this->response(["error" => "El email o password son incorrectos"]);
        }

        if ($user["password"] === $usuario["password"]) {
            unset($user["password"]);
            $this->response(["token" => $this->_create_token($user), "usuario" => $user]);
        } else {
            $this->response(["error" => "El password o email son incorrectos"]);
        }
    }

    public function rol_post() {
        $headers = $this->input->request_headers();

        if (!array_key_exists('Authorization', $headers)) {
            $this->response(["error" => ["message" => "Inicie sesión para acceder a los recursos del sistema"]], REST_Controller::HTTP_UNAUTHORIZED);
        }

        $authorization = $headers['Authorization'];
        $token = explode(' ', $authorization)[1];
        $jwt = JOSE_JWT::decode($token);
        $jws = new JOSE_JWS($jwt);
        //throw Exception  Signature Verification Failed en caso de fallo
        $jws->verify($this->config->item('token_secret'));

        if ($jws->claims['exp'] < time()) {
            $this->response(["error" => ["message" => "Su sesión ha caducado"]], REST_Controller::HTTP_UNAUTHORIZED);
        } else {
            $this->response($jws->claims['rol']);
        }
    }

}
