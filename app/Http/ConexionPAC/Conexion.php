<?php

namespace App\Http\ConexionPAC;

class Conexion {

    public function obtenerTimbrado($type, $xmlString){
        
        $url ='';
        $user ='';
        $password ='';
        if($type == "TEST"){
            $url = 'https://timbradopruebas.stagefacturador.com/timbrado.asmx?WSDL';
            $user = 'test';
            $password = 'TEST';
        }else if ($type == "PROD"){
            $url = 'https://timbrado.facturador.com/timbrado.asmx?WSDL';
            $user = 'GilKatzyn';
            $password = 'ZpaneTx6';
        }
        $client = new \nusoap_client($url, true);
        $client->decode_utf8 = FALSE;
        $client->soap_defencoding = 'UTF-8';

        $params = array(
            'CFDIcliente' => $xmlString,
            'Usuario' => $user,
            'password' => $password
        );

        return $client->call('obtenerTimbrado', $params);
    }
}