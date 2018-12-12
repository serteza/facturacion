<?php

namespace App\Http\Controllers;

use App\Http\GenXML\GenXML;
use Illuminate\Http\Request;
use App\Http\ConexionPAC\Conexion;
use Illuminate\Support\Facades\Storage;

class FacturasController extends Controller
{
    public function facturas(Request $req){


      $genXML = new GenXML();
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
      $xml = $genXML->xmlHeader($xml, "factura", $req->Comprobante, $serialNumber, $b4cer);
      //se genera el emisor y receptor
      $xml = $genXML->xmlEmisorReceptor($xml, $req->Emisor, $req->Receptor);

      $xml = $genXML->xmlConceptosFactura($xml, $req->Conceptos);

      $xml = $genXML->cadenaOriginalYSello($xml, $req->Emisor);

      $xml = $genXML->xmlImpuestos($xml, $req->Impuestos);

      $xmlString = trim($xml->saveXML());

      return response()->make(($xmlString), 200, ['Content-Type' => 'application/xml']);

      $resultado = $connectPAC->obtenerTimbrado($req->query('type'), $xmlString);

      $response = json_decode(json_encode($resultado, true))->obtenerTimbradoResult->timbre;
      $isValid = $response->{'!esValido'};
      if($isValid == "True"){

          $xml = $genXML->xmlTimbreFiscal($xml, $response->TimbreFiscalDigital);
          $xmlTimbreFiscal = trim($xml->saveXML());

          //return response()->json(["timbre" => $Certificado],200);
          return response()->make(($xmlTimbreFiscal), 200, ['Content-Type' => 'application/xml']);
      }else{
          return response()->json(["timbre" => $response->errores],200);
      }

    
    }
}
