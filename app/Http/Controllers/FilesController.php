<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function storeKeys(Request $req){

        $dir = $req->rfc;
        $cerFile = $req->cer;
        $keyFile = $req->key;
        $passphrase = $req->pass;
        $path = storage_path('app/keys/'.$dir);
        $response = " ";

        if(is_dir($path)){

            $response = $this->checkAndStore($response, $dir, $cerFile, $keyFile);

            $response = $this->createPem($path, $dir, $response);

            $response = $this->createPass($path, $dir, $passphrase,$response);

            $response = $this->createKeyPem($path, $dir, $response);

            return response()->json(["message" => $response],200);

        }else{

            mkdir($path, 0755, true);
            $response .= " Directorio creado, ";

            $response = $this->checkAndStore($response, $dir, $cerFile, $keyFile);

            $response = $this->createPem($path, $dir, $response);

            $response = $this->createPass($path, $dir, $passphrase,$response);
            
            $response = $this->createKeyPem($path, $dir, $response);

            return response()->json(["message" => $response],200);
        }
        
    }

    private function checkAndStore($response, $dir, $cerFile, $keyFile){

        if(!Storage::disk('local')->exists($dir."/".$dir.".cer")){

            Storage::disk('local')->put($dir."/".$dir.".cer", file_get_contents($cerFile),'public');

            $response .= " El archivo .cer guardado exitoso ";
        
        } else {
            $response .= " El archivo .cer ya existe ";
        }

        if(!Storage::disk('local')->exists($dir."/".$dir.".key")){

            Storage::disk('local')->put($dir."/".$dir.".key", file_get_contents($keyFile),'public');

            $response .= ", el archivo .key guardado exitoso ";
        
        } else {
            $response .= ", el archivo .key ya existe ";
        }

        return $response;
    }

    private function createPass($path, $dir, $passphrase,$response){

        if(!Storage::disk('local')->exists($dir."/".$dir.".txt")){

            $hashed = $this->encrypt_decrypt('encrypt', $passphrase);

            $encrypted = Crypt::encryptString($hashed);

            Storage::disk('local')->put($dir."/".$dir.".txt", $encrypted, 'public');

            //error_log($encrypted);
            $response .= ", el archivo .der creado ";

            return $response;
        }else{
            $response .= ", el archivo .der ya existe ";
        }

        return $response;
        
    }

    private function createPem($path, $dir, $response){

        if(Storage::disk('local')->exists($dir."/".$dir.".cer")){
            $pathTocreate = $path."/".$dir.".cer";
            $cerFile =  file_get_contents($pathTocreate);
            $pemFile = "-----BEGIN CERTIFICATE-----\r\n" . chunk_split(base64_encode($cerFile), 64) . '-----END CERTIFICATE-----';
            //error_log($pemFile);
            Storage::disk('local')->put($dir."/".$dir.".pem", $pemFile, 'public');
            $response .= ", el archivo .pem creado ";

            return $response;
        }
        
    }

    private function createKeyPem($path, $dir, $response){

        if(Storage::disk('local')->exists($dir."/".$dir.".key")){

            $pathTopassphrase = $path."/".$dir.".txt";
            $passFile =  file_get_contents($pathTopassphrase);
            $decrypted = Crypt::decryptString($passFile);
            $hashed = $this->encrypt_decrypt('decrypt', $decrypted);
            //error_log($hashed);
            $passphrase = $hashed;

            $pathTocreate = $path."/".$dir.".key";
            $fileOut = $path."/".$dir.".key.pem";
            exec("openssl pkcs8 -inform DER -in ".$pathTocreate." -out ".$fileOut." -passin pass:".$passphrase);
            $response .= ", el archivo .key.pem creado ";

            return $response;
        }
        
    }

    function encrypt_decrypt($action, $string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9';
        $secret_iv = 'ngFIIlqydhXQV6PvXtAkLosyCSiWq4pg0OuwGvYRI';
        // hash
        $key = hash('sha256', $secret_key);
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }
    
}
