<?php

namespace App\Http\Controllers;

use App\Http\GenXML\GenXMLDevol;
use Illuminate\Http\Request;
use App\Http\ConexionPAC\Conexion;
use Illuminate\Support\Facades\Storage;

class RetencionesController extends Controller
{
    public function retenciones(Request $req){


      $genXML = new GenXMLDevol();
      $connectPAC = new Conexion();
      $xml = new \DOMDocument("1.0","UTF-8");
      
      $pem = Storage::get($req->Emisor['Rfc'].'/'.$req->Emisor['Rfc'].'.pem');
      $Certificado = openssl_x509_parse($pem,true);

      $parseSerialNumber= $Certificado['serialNumberHex'];
      $serialNumber = '';
      for($i=1;$i<strlen($parseSerialNumber);$i=$i+2){
          $serialNumber = $serialNumber.''.$parseSerialNumber[$i];
      }

      $rfcEmisor = preg_split('[/.-]', $Certificado['subject']['x500UniqueIdentifier'])[0];
      $razonEmisor = $Certificado['subject']['name'];
      //convertimos certificado a base64
      $pathCer = Storage::get($req->Emisor['Rfc'].'/'.$req->Emisor['Rfc'].'.cer');
      $b4cer = base64_encode($pathCer);

      //se genera el header cfdi:Comprobante
      $xml = $genXML->xmlHeader($xml, "retenciones", $req->Comprobante, $serialNumber, $b4cer);
      //se genera el emisor y receptor
      $xml = $genXML->xmlEmisorReceptor($xml, $req->Emisor, $req->Receptor);

      $xml = $genXML->xmlPeriodos($xml, $req->Periodo);
    
      $xml = $genXML->xmlImpuestos($xml, $req->Impuesto);
      echo "aqui";
      $xml = $genXML->cadenaOriginalYSello($xml, $req->Emisor);
      
      echo "aqui".$xml;

      $xmlString = trim($xml->saveXML());
      echo "aqui";

      return response()->make(($xmlString), 200, ['Content-Type' => 'application/xml']);

      $resultado = $connectPAC->obtenerTimbrado($req->query('type'), $xmlString);

      $response = json_decode(json_encode($resultado, true))->obtenerTimbradoResult->timbre;
      $isValid = $response->{'!esValido'};
      if($isValid == "True"){
        echo "aqui";

          $xml = $genXML->xmlTimbreFiscal($xml, $response->TimbreFiscalDigital, 'factura');
          $xmlTimbreFiscal = trim($xml->saveXML());

          //return response()->json(["timbre" => $Certificado],200);
          return response()->make(($xmlTimbreFiscal), 200, ['Content-Type' => 'application/xml']);
      }else{
          return response()->json(["timbre" => $response->errores],200);
      }

    
    }
}
