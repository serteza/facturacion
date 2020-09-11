<?php

namespace App\Http\GenXML;
use Illuminate\Support\Facades\Storage;
use App\Http\GenCadenaOriginal\GenCadenaDevoluciones;

class GenXMLDevol {

    public function xmlHeader($xml, $tipo, $Comprobante, $serialNumber, $b4cer){

     
        $comprobante = json_decode(json_encode($Comprobante, true));
        
        $nodoComprobante = $xml->createElement('retenciones:Retenciones');

        $schemaLocation="";

        
        $nodoComprobante->setAttribute("xmlns:retenciones", "http://www.sat.gob.mx/esquemas/retencionpago/1");
        $schemaLocation="http://www.sat.gob.mx/esquemas/retencionpago/1 http://www.sat.gob.mx/esquemas/retencionpago/1/retencionpagov1.xsd";
        $nodoComprobante->setAttribute('xsi:schemaLocation', $schemaLocation);
        $nodoComprobante->setAttribute('DescRetenc', $comprobante->descripcionRetencion);
        $nodoComprobante->setAttribute('CveRetenc', $comprobante->claveRetencion);
        $nodoComprobante->setAttribute('FechaExp', $comprobante->Fecha);
        $nodoComprobante->setAttribute('Cert', $b4cer);
        $nodoComprobante->setAttribute('NumCert', $serialNumber);
        $nodoComprobante->setAttribute('Sello', '');
        $nodoComprobante->setAttribute('FolioInt', $comprobante->Folio);
        $nodoComprobante->setAttribute('Version', '1.0');
        $nodoComprobante->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        
        
        
        
        //codigo postal, viene de catalogo
        //$nodoComprobante->setAttribute('LugarExpedicion', $comprobante->LugarExpedicion);
        //$nodoComprobante->setAttribute('xmlns:cfdi', 'http://www.sat.gob.mx/cfd/3');
        
        $xml->appendChild($nodoComprobante);

        return $xml;
    }

    public function xmlEmisorReceptor($xml, $Emisor, $Receptor){
        
        $emisor = json_decode(json_encode($Emisor, true));
        $receptor = json_decode(json_encode($Receptor, true));

        $nodoEmisor = $xml->createElement('retenciones:Emisor');

        $nodoEmisor->setAttribute('RFCEmisor', $emisor->Rfc);
        $nodoEmisor->setAttribute('NomDenRazSocE', $emisor->Nombre);
        
        $nodoReceptor = $xml->createElement('retenciones:Receptor');
        $nodoReceptor->setAttribute('Nacionalidad', $receptor->Nacionalidad);
        $nodoNacional = $xml->createElement('retenciones:Nacional');
        $nodoNacional->setAttribute('NomDenRazSocR', $receptor->Nombre);
        $nodoNacional->setAttribute('RFCRecep', $receptor->Rfc);

        $nodoReceptor->appendChild($nodoNacional);

        $getNodoComprobante = $xml->firstChild;
        $getNodoComprobante->appendChild($nodoEmisor);
        $getNodoComprobante->appendChild($nodoReceptor);

        return $xml;
    }

    public function xmlPeriodos($xml,$Periodos){
        
        $Periodos = json_decode(json_encode($Periodos, true));
       
        $nodoPeriodo = $xml->createElement('retenciones:Periodo');
        $nodoPeriodo->setAttribute('Ejerc', $Periodos->Ejercicio);
        $nodoPeriodo->setAttribute('MesFin', $Periodos->MesFinal);
        $nodoPeriodo->setAttribute('MesIni', $Periodos->MesInicial);
        
        $getNodoComprobante = $xml->firstChild;
        $getNodoComprobante->appendChild($nodoPeriodo);

        return $xml;
    }

    public function xmlImpuestos($xml, $Impuestos){
        $Impuestos = json_decode(json_encode($Impuestos, true));
        $nodoTotales = $xml->createElement('retenciones:Totales');
        $nodoTotales->setAttribute('montoTotRet', $Impuestos->montoRetenido);
        $nodoTotales->setAttribute('montoTotExent', $Impuestos->montoExento);
        $nodoTotales->setAttribute('montoTotGrav', $Impuestos->montoGravado);
        $nodoTotales->setAttribute('montoTotOperacion', $Impuestos->montoGravado + $Impuestos->montoExento);

        $nodoImpuestos = $xml->createElement('retenciones:ImpRetenidos');
        $nodoImpuestos->setAttribute('TipoPagoRet', $Impuestos->conceptoRetencion);
        $nodoImpuestos->setAttribute('montoRet', $Impuestos->montoRetenido);
        $nodoImpuestos->setAttribute('Impuesto', $Impuestos->tipoImpuesto);
        $nodoImpuestos->setAttribute('BaseRet', $Impuestos->montoGravado);

        $nodoTotales->appendChild($nodoImpuestos);


        $getNodoComprobante = $xml->firstChild;
        $getNodoComprobante->appendChild($nodoTotales);

        return $xml;
    }

    public function cadenaOriginalYSello($xml, $Emisor){
        
        $emisor = json_decode(json_encode($Emisor, true));
        $genCadenaOriginal = new GenCadenaDevoluciones();
        $xmlString = trim($xml->saveXML());
        
        $cadena = $genCadenaOriginal->cadenaOriginal($xmlString);
        
        error_log($cadena);
        
        $sello = $this->Sello($cadena, $emisor->Rfc);

        $getNodoComprobante = $xml->firstChild;
        $getNodoComprobante->setAttribute('Sello', $sello);
        //print_r("CADENA ORIGINAL");
        return $xml;
    }


    public function xmlTimbreFiscal($xml, $TimbreFiscalDigital, $type){

        $nodoTimbreFiscal = $xml->createElement('tfd:TimbreFiscalDigital');
        
        $nodoTimbreFiscal->setAttribute('xmlns:tfd', 'http://www.sat.gob.mx/TimbreFiscalDigital');
        $nodoTimbreFiscal->setAttribute('xsi:schemaLocation', $TimbreFiscalDigital->{'!xsi:schemaLocation'});
        $nodoTimbreFiscal->setAttribute('Version', $TimbreFiscalDigital->{'!Version'});
        $nodoTimbreFiscal->setAttribute('UUID', $TimbreFiscalDigital->{'!UUID'});
        $nodoTimbreFiscal->setAttribute('FechaTimbrado', $TimbreFiscalDigital->{'!FechaTimbrado'});
        $nodoTimbreFiscal->setAttribute('RfcProvCertif', $TimbreFiscalDigital->{'!RfcProvCertif'});
        $nodoTimbreFiscal->setAttribute('SelloCFD', $TimbreFiscalDigital->{'!SelloCFD'});
        $nodoTimbreFiscal->setAttribute('NoCertificadoSAT', $TimbreFiscalDigital->{'!NoCertificadoSAT'});
        $nodoTimbreFiscal->setAttribute('SelloSAT', $TimbreFiscalDigital->{'!SelloSAT'});

        
            $nodoComplemento = $xml->createElement('retenciones:Complemento');
            $nodoComplemento->appendChild($nodoTimbreFiscal);
            
            $getNodoComprobante = $xml->firstChild;
            $getNodoComprobante->appendChild($nodoComplemento);

        return $xml;
    }

    private function Sello($cadena, $EmisorRFC){
        
        $keyPem = Storage::get($EmisorRFC.'/'.$EmisorRFC.'.key.pem');
        //$keyPem = Storage::url($EmisorRFC.'/'.$EmisorRFC.'.key.pem');

        $private = openssl_pkey_get_private($keyPem);

        openssl_sign($cadena, $sig, $private, OPENSSL_ALGO_SHA256);
        //error_log($cadena);
        //error_log(utf8_encode($sig));
        $sello = base64_encode($sig);
        
        return $sello;
    }
}
