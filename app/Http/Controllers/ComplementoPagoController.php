<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\ConexionPAC\Conexion;
use App\Http\GenXML\GenXML;
use App\Http\GenPDF\GenPDF;
use DateTime;

class ComplementoPagoController extends Controller
{
    public function complementoPagos(Request $req){
        
        $genXML = new GenXML();
        $connectPAC = new Conexion();
        $xml = new \DOMDocument("1.0","UTF-8");
    
        $pem = Storage::get($req->Emisor['Rfc'].'/'.$req->Emisor['Rfc'].'.pem');
        $Certificado = openssl_x509_parse($pem,true);

        //return response()->json(["timbre" => $Certificado],200);

        //error_log( print_r($Certificado, TRUE) );
        $parseSerialNumber= $Certificado['serialNumberHex'];

        /*$priv_key = Storage::get($req->Emisor['Rfc'].'/CSD_A_CIN140605QM1_20160926_201703.key');
        $passphrase ="crh140605";
        $keys = openssl_pkey_new(array("digest_alg" => 'sha512',
                                       "private_key_bits" => 1024,
                                       "private_key_type" => OPENSSL_KEYTYPE_RSA));

        openssl_pkey_export($keys, $privkey, $passphrase );
        
        //openssl_private_decrypt($crypttext,$newsource,$res);

        error_log( $privkey);*/
    
        //obtenemos numero de certificado
        $serialNumber = '';
        for($i=1;$i<strlen($parseSerialNumber);$i=$i+2){
            $serialNumber = $serialNumber.''.$parseSerialNumber[$i];
        }
        //error_log($serialNumber);
        //obtenemos rfc y razon social del csd
        $rfcEmisor = preg_split('[/.-]', $Certificado['subject']['x500UniqueIdentifier'])[0];
        $razonEmisor = $Certificado['subject']['name'];
        //convertimos certificado a base64
        $pathCer = Storage::get($req->Emisor['Rfc'].'/'.$serialNumber.'.cer');
        $b4cer = base64_encode($pathCer);
        //se genera el header cfdi:Comprobante
        $xml = $genXML->xmlHeader($xml, $req->Comprobante, $serialNumber, $b4cer);
        //se genera el emisor y receptor
        $xml = $genXML->xmlEmisorReceptor($xml, $req->Emisor, $req->Receptor);
        //conceptos
        $xml = $genXML->xmlConceptos($xml);
        //complemento y pagos
        $xml = $genXML->xmlComplementoPago($xml,$req->Pago, $req->DoctoRelacionado);
        //generacion de la cadena original del complemento de pago
        $xml = $genXML->cadenaOriginalCompPago($xml,$req->Comprobante,$serialNumber,
                                            $req->Emisor, $req->Receptor,$req->Pago,
                                            $req->DoctoRelacionado);

        $xmlString = trim($xml->saveXML());

        //$resultado = $connectPAC->obtenerTimbrado($req->query('type'), $xmlString);
        $resultado = '{
            "obtenerTimbradoResult": {
                "timbre": {
                    "TimbreFiscalDigital": {
                        "!xsi:schemaLocation": "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigitalv11.xsd",
                        "!Version": "1.1",
                        "!UUID": "BBCFC65E-3DCE-4D72-AF5A-45DCABDB36C6",
                        "!FechaTimbrado": "2018-11-22T18:29:30",
                        "!RfcProvCertif": "DND070112H92",
                        "!SelloCFD": "dTjdIEtxWazQICFQm++H1ON/FJItp2R476S7bN37Bmm5E0Bc2qRvON6aF2+LeBVEmmJSMgeUGzA7HXEgOxLSn57wGdJysrpbGZt6TIk0+EVWvuHC1UE0Nbjp00ZKUjyBi8LEJfnTo20l3E8yEod9KwZAeuodb10LbcON2mbpEBsAfa2go4DulFwUXfPxCANUUMgVDv6cjztIalErT7vbLlzoGueZzc/DkMTNxic+qghmUSkQLGb8oajS6CFC8zt3wDDKTI6/NdzgOT/8BgEIUaBmzKWu2SlxJKEN+WRaiYWeSxpkQc6HHQLcOdOF2+HtewGzb1zF3eRpEn/e+QjZcw==",
                        "!NoCertificadoSAT": "00001000000405908583",
                        "!SelloSAT": "kkFHiEVjzYwql7gXbe3P9VAZp3FBFG8Bm3QoCjIaRS5dxemyaHGCNB8Q/4ab7cPjK5+gBRtjBDHB0oc50Wmap5caWwTO0sh9VTD5AjhmhlEhnRllo8JiJVRGP5RGJoyHCtlpAdtDNRszbtJbl4vNBRiMeAeKGN6IuHKU7pTYEo8QTElRSNzjStS7D+1K/zmT+7cSUxavP1yLNTThl4eYVIGX3V0ZzFOzWa/VR/pJU3UfsrGZVEEDAvkakK8VxWK2vogG5D1FHNeSFyynOZR4XktacXtOviMucXoBpX+EHlZRQy+I6DiP8mt7A7S7+EhP1S0bz2pWx6UsBA3vDTaWdw=="
                    },
                    "!esValido": "True"
                }
            }
        }';
            
        $resultado = json_decode($resultado, true);
    
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

    public function pdfGenerator( $uuid, $rfc){

        $genPDF = new GenPDF();
        $filename = $uuid;
        $now = new DateTime();
        $nombre ='CRH INNOVACION & SCP';
        $fechaExp = $now->format('Y-m-d H:i:s');
        $noCertEmisor='00001000000403772922';
        $noCertSAT='00001000000405908583';
        $fechaCert = $now->format('Y-m-d H:i:s');
        $nombreReceptor ='SERVICIOS MULTIPLES DE PERSONAL TECNIC Y OPERATIVOS S DE RL DE CV';
        if(strlen($nombreReceptor) > 56){
            $nombreReceptor = substr( $nombreReceptor, 0, 55) . '...';
        }
        $logo = 'data://text/plain;base64,'.base64_encode(Storage::disk('logos')->get($rfc.'.jpg'));
        $qr = 'data://text/plain;base64,'.base64_encode(\QrCode::format('png')
                                                        ->size(399)
                                                        ->generate('
                                                        https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx/'
                                                        .$uuid.'&'
                                                        .'re='.$rfc.'&'
                                                        .'rr=SMP090115A50'.'&tt=0&fe=FALD9g=='));
        //$qr = 'data://text/plain;base64,'.base64_encode(Storage::disk('logos')->get('logo_serteza.jpg'));
        $header = '{
            "logo": "'.$logo.'",
            "qr": "'.$qr.'",
            "nombre":"'.$nombre.'",
            "fechaExp":"'.$fechaExp.'",
            "fechaCert":"'.$fechaCert.'",
            "noCertEmisor":"'.$noCertEmisor.'",
            "noCertSAT":"'.$noCertSAT.'",
            "uuid":"'.$uuid.'",
            "cp":"97000"
        }';
        $header = json_decode($header, true);

        $datosReceptorFacComp = '{
            "rfc": "'.$rfc.'",
            "serieFolio": "P 2",
            "nombreReceptor": "'.$nombreReceptor.'",
            "fecha":"'.$fechaExp.'",
            "direccion":"CONOCIDA",
            "cerCSD":"'.$noCertEmisor.'",
            "formaPago":"01",
            "noCuenta":"------",
            "metodoPago":"Pago en parcialidades (PPD)",
            "usoCFDI":"Gastos en general (G03)",
            "tipoMoneda":"MXN"
        }';

        $datosReceptorFacComp = json_decode($datosReceptorFacComp, true);

    $pdf=new \FPDF('P','mm','Letter');
    $pdf->AddPage();
    
    $pdf = $genPDF->headerComprobante($pdf, $header);
    
    $pdf = $genPDF->datosReceptorFacComprobante($pdf, $datosReceptorFacComp);

    $pdf = $genPDF->datosComp($pdf);

    $pdf = $genPDF->footerComp($pdf, $qr);

    return response()->make(($pdf->Output('', "S")), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline;']);
    
    }

}
