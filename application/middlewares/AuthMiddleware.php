<?php

//require_once (APPPATH . 'libraries/REST_Controller.php');
require(APPPATH . 'libraries/jose/JWT.php');
require(APPPATH . 'libraries/jose/JWS.php');
require(APPPATH . 'libraries/jose/URLSafeBase64.php');
require(APPPATH . 'libraries/jose/Exception.php');
require(APPPATH . 'libraries/jose/Exception/VerificationFailed.php');
require(APPPATH . 'libraries/jose/Exception/InvalidFormat.php');
require(APPPATH . 'libraries/jose/Exception/UnexpectedAlgorithm.php');
require(APPPATH . 'libraries/jose/Exception/DecryptionFailed.php');

class AuthMiddleware {

    protected $controller;
    protected $ci;
    //propiedad que pasaremos al controller
    public $claims;

    public function __construct($controller, $ci) {
        $this->controller = $controller;
        $this->ci = $ci;
    }

    public function run() {
        //$this->roles = array('somehting', 'view', 'edit'); 

        $headers = $this->controller->input->request_headers(TRUE);

        //debug
        //$server=$_SERVER;
        //$this->controller->response("headers" => $headers,"server"=>$server], REST_Controller::HTTP_UNAUTHORIZED);
        //if (!array_key_exists('X-Authorization', $headers)) {
        if (!array_key_exists('Authorization', $headers)) {
            $this->controller->response(["error" => ["message" => "Inicie sesión para acceder a los recursos del sistema"]], REST_Controller::HTTP_UNAUTHORIZED);
        }

        //$authorization = $headers['X-Authorization'];
        $authorization = $headers['Authorization'];
        $token = explode(' ', $authorization)[1];
        $jwt = JOSE_JWT::decode($token);
        $jws = new JOSE_JWS($jwt);
        //throw Exception  Signature Verification Failed en caso de fallo
        //$this->controller->config->item('token_secret')
        $jws->verify($this->controller->config->item('token_secret'));

        if ($jws->claims['exp'] < time()) {
            $this->controller->response(["error" => ["message" => "Su sesión ha caducado"]], REST_Controller::HTTP_UNAUTHORIZED);
        }

        //luego en el controller podemos acceder a la propiedad
        //$this->middlewares['auth']->claims
        $this->claims = $jws->claims;
    }

}
