<?php

namespace App\Http\Controllers;

use phpseclib\Crypt\RSA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function storeKeys(Request $req){

        $dir = $req->rfc;
        $cerFile = $req->cer;
        $keyFile = $req->key;
        $path = storage_path('app/keys/'.$dir);
        $response = " ";

        if(is_dir($path)){

            $response = $this->checkAndStore($response, $dir, $cerFile, $keyFile);

            $response = $this->createPem($path, $dir, $response);

            $response = $this->createKeyPem($path, $dir, $response);

            return response()->json(["message" => $response],200);

        }else{

            mkdir($path, 0755, true);
            $response .= " Directorio creado, ";

            $response = $this->checkAndStore($response, $dir, $cerFile, $keyFile);

            $response = $this->createPem($path, $dir, $response);

            $response = $this->createKeyPem($path, $dir, $response);

            return response()->json(["message" => $response],200);
        }
        
    }

    private function checkAndStore($response, $dir, $cerFile, $keyFile){

        if(!Storage::disk('local')->exists($dir."/".$dir.".cer")){

            Storage::disk('local')->put($dir."/".$dir.".cer", file_get_contents($cerFile));

            $response .= " El archivo .cer guardado exitoso ";
        
        } else {
            $response .= " El archivo .cer ya existe ";
        }

        if(!Storage::disk('local')->exists($dir."/".$dir.".key")){

            Storage::disk('local')->put($dir."/".$dir.".key", file_get_contents($keyFile));

            $response .= ", el archivo .key guardado exitoso ";
        
        } else {
            $response .= ", el archivo .key ya existe ";
        }

        return $response;
    }

    private function createPem($path, $dir, $response){

        if(Storage::disk('local')->exists($dir."/".$dir.".cer")){
            $pathTocreate = $path."/".$dir.".cer";
            $cerFile =  file_get_contents($pathTocreate);
            $pemFile = "-----BEGIN CERTIFICATE-----\r\n" . chunk_split(base64_encode($cerFile), 64) . '-----END CERTIFICATE-----';
            //error_log($res);
            Storage::disk('local')->put($dir."/".$dir.".pem", $pemFile);
            $response .= ", el archivo .pem creado ";

            return $response;
        }
        
    }

    private function createKeyPem($path, $dir, $response){

        if(Storage::disk('local')->exists($dir."/".$dir.".key")){
        
            $passphrase ="crh140605";

            $pathTocreate = $path."/".$dir.".key";
            $keyFile =  file_get_contents($pathTocreate);

            $rsa = new RSA();
            $rsa->setPassword($passphrase);
            $rsa->loadKey($keyFile);
            $private = openssl_pkey_get_private($rsa->getPrivateKey(), $passphrase);
            error_log($private);
            /*
            $fileKey = Principal::$AppPath . "docs/sat/ACO560518KW7-20001000000300005692.key"; // Ruta al archivo key
            $rsa = new RSA();
            $rsa->setPassword("12345678a");//Clave 
            $rsa->load(file_get_contents($fileKey));
            $private = openssl_pkey_get_private($rsa->getPrivateKey(), "12345678a");//Otra vez la clave
            $sig = "";
            openssl_sign($this->CadenaOriginal, $sig, $private, OPENSSL_ALGO_SHA256);
            $sello = base64_encode($sig);
            $this->Comprobante->Sello = $sello;
            */

            //$res=openssl_pkey_new();

            // Get private key
            //openssl_pkey_export($keyFile, $res, $passphrase);

            //$keypemFile = "-----BEGIN PRIVATE KEY-----\r\n" . chunk_split(base64_encode($keyFile), 64) . "-----END PRIVATE KEY-----";
            return response()->json(["message" => $private],200);
            //exec("openssl pkcs8 -inform DER -in ".$pathTocreate." -out ".$fileOut." -passin pass:".$passphrase);
            $response .= ", el archivo .key.pem creado ";

            return $response;
        }
        
    }
}
