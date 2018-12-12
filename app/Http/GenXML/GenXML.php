<?php

namespace App\Http\GenXML;
use Illuminate\Support\Facades\Storage;
use App\Http\GenCadenaOriginal\GenCadenaOriginal;

class GenXML {

    public function xmlHeader($xml, $tipo, $Comprobante, $serialNumber, $b4cer){

        $comprobante = json_decode(json_encode($Comprobante, true));
        
        $nodoComprobante = $xml->createElement('cfdi:Comprobante');

        $schemaLocation="";

        if($tipo == "complemento"){
            $nodoComprobante->setAttribute("xmlns:pago10", "http://www.sat.gob.mx/Pagos");
            $schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd http://www.sat.gob.mx/Pagos http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos10.xsd";
        } else if ($tipo == "factura"){
            $schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd";
        }

        $nodoComprobante->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $nodoComprobante->setAttribute('xsi:schemaLocation', $schemaLocation);
        $nodoComprobante->setAttribute('Version', '3.3');
        $nodoComprobante->setAttribute('Serie', $comprobante->Serie);
        $nodoComprobante->setAttribute('Folio', $comprobante->Folio);
        //fecha actual
        $nodoComprobante->setAttribute('Fecha', $comprobante->Fecha);
        $nodoComprobante->setAttribute('Sello', '');

        if ($tipo == "factura"){
            $nodoComprobante->setAttribute('FormaPago', $comprobante->FormaPago);      
        }
        $nodoComprobante->setAttribute('NoCertificado', $serialNumber);
        $nodoComprobante->setAttribute('Certificado', $b4cer);
        
        if ($tipo == "factura"){
            if( $comprobante->Descuento != "" && ($comprobante->TipoDeComprobante == "I" || $comprobante->TipoDeComprobante == "E")){
                $nodoComprobante->setAttribute('CondicionesDePago', $comprobante->CondicionesDePago); 
            }     
        }

        if ($tipo == "factura"){
            $nodoComprobante->setAttribute('SubTotal', $comprobante->SubTotal);   
        }else{
            $nodoComprobante->setAttribute('SubTotal', '0');
        }

        if ($tipo == "factura"){
            if( $comprobante->Descuento != "" && ($comprobante->TipoDeComprobante != "T" || $comprobante->TipoDeComprobante != "P")){
                $nodoComprobante->setAttribute('Descuento', $comprobante->Descuento); 
            }     
        }
        
        if($tipo == "factura"){
            $nodoComprobante->setAttribute('Moneda', $comprobante->Moneda);
            $nodoComprobante->setAttribute('Total', $comprobante->Total);
            $nodoComprobante->setAttribute('TipoDeComprobante', $comprobante->TipoDeComprobante);
            $nodoComprobante->setAttribute('MetodoPago', $comprobante->MetodoPago);
        }else {
            $nodoComprobante->setAttribute('Moneda', 'XXX');
            $nodoComprobante->setAttribute('Total', '0');
            $nodoComprobante->setAttribute('TipoDeComprobante', 'P');
        }
        //codigo postal, viene de catalogo
        $nodoComprobante->setAttribute('LugarExpedicion', $comprobante->LugarExpedicion);
        
        $nodoComprobante->setAttribute('xmlns:cfdi', 'http://www.sat.gob.mx/cfd/3');
        
        $xml->appendChild($nodoComprobante);

        return $xml;
    }

    public function xmlEmisorReceptor($xml, $Emisor, $Receptor){
        
        $emisor = json_decode(json_encode($Emisor, true));
        $receptor = json_decode(json_encode($Receptor, true));

        $nodoEmisor = $xml->createElement('cfdi:Emisor');

        $nodoEmisor->setAttribute('Rfc', $emisor->Rfc);
        $nodoEmisor->setAttribute('Nombre', $emisor->Nombre);
        $nodoEmisor->setAttribute('RegimenFiscal', $emisor->RegimenFiscal);

        $nodoReceptor = $xml->createElement('cfdi:Receptor');

        $nodoReceptor->setAttribute('Rfc', $receptor->Rfc);
        $nodoReceptor->setAttribute('Nombre', $receptor->Nombre);
        $nodoReceptor->setAttribute('UsoCFDI', $receptor->UsoCFDI);

        $getNodoComprobante = $xml->firstChild;
        $getNodoComprobante->appendChild($nodoEmisor);
        $getNodoComprobante->appendChild($nodoReceptor);

        return $xml;
    }

    public function xmlConceptos($xml){
        
        $nodoConceptos = $xml->createElement('cfdi:Conceptos');

        $nodoConcepto = $xml->createElement('cfdi:Concepto');
        $nodoConcepto->setAttribute('ClaveProdServ', '84111506');
        $nodoConcepto->setAttribute('Cantidad', '1');
        $nodoConcepto->setAttribute('ClaveUnidad', 'ACT');
        $nodoConcepto->setAttribute('Descripcion', 'Pago');
        $nodoConcepto->setAttribute('ValorUnitario', '0');
        $nodoConcepto->setAttribute('Importe', '0');

        $nodoConceptos->appendChild($nodoConcepto);
        $getNodoComprobante = $xml->firstChild;
        $getNodoComprobante->appendChild($nodoConceptos);

        return $xml;
    }

    public function xmlConceptosFactura($xml, $Conceptos){

        $conceptos = json_decode(json_encode($Conceptos, true));
        
        $nodoConceptos = $xml->createElement('cfdi:Conceptos');

        foreach ($conceptos as $key => $concepto) {
            $nodoConcepto = $xml->createElement('cfdi:Concepto');

            $nodoConcepto->setAttribute('ClaveProdServ', $concepto->ClaveProdServ);
            $nodoConcepto->setAttribute('ClaveUnidad', $concepto->ClaveUnidad);
            $nodoConcepto->setAttribute('Cantidad', $concepto->Cantidad);
            $nodoConcepto->setAttribute('Descripcion', $concepto->Descripcion);
            $nodoConcepto->setAttribute('ValorUnitario', $concepto->ValorUnitario);
            $nodoConcepto->setAttribute('Importe', $concepto->Importe);
            $nodoConcepto->setAttribute('Descuento', $concepto->Descuento);
            //error_log(print_r($concepto->Impuestos, TRUE));

            $nodoImpuestos = $xml->createElement('cfdi:Impuestos');

                if (isset($concepto->Impuestos->Traslado)) {
                    $nodoTraslados = $xml->createElement('cfdi:Traslados');
                        $nodoTraslado = $xml->createElement('cfdi:Traslado');
                            $nodoTraslado->setAttribute('Base', $concepto->Impuestos->Traslado->Base);
                            $nodoTraslado->setAttribute('Impuesto', $concepto->Impuestos->Traslado->Impuesto);
                            $nodoTraslado->setAttribute('TipoFactor', $concepto->Impuestos->Traslado->TipoFactor);
                            $nodoTraslado->setAttribute('TasaOCuota', $concepto->Impuestos->Traslado->TasaOCuota);
                            $nodoTraslado->setAttribute('Importe', $concepto->Impuestos->Traslado->Importe);
                            $nodoTraslados->appendChild($nodoTraslado);
                    $nodoImpuestos->appendChild($nodoTraslados);
                }
                if (isset($concepto->Impuestos->Retencion)) {
                    $nodoRetenciones = $xml->createElement('cfdi:Retenciones');
                        $nodoRetencion = $xml->createElement('cfdi:Retencion');
                            $nodoRetencion->setAttribute('Base', $concepto->Impuestos->Retencion->Base);
                            $nodoRetencion->setAttribute('Impuesto', $concepto->Impuestos->Retencion->Impuesto);
                            $nodoRetencion->setAttribute('TipoFactor', $concepto->Impuestos->Retencion->TipoFactor);
                            $nodoRetencion->setAttribute('TasaOCuota', $concepto->Impuestos->Retencion->TasaOCuota);
                            $nodoRetencion->setAttribute('Importe', $concepto->Impuestos->Retencion->Importe);
                        $nodoRetenciones->appendChild($nodoRetencion);
                    $nodoImpuestos->appendChild($nodoRetenciones);
                }


            $nodoConcepto->appendChild($nodoImpuestos);
        
            $nodoConceptos->appendChild($nodoConcepto);
        }

        $getNodoComprobante = $xml->firstChild;
        $getNodoComprobante->appendChild($nodoConceptos);

        return $xml;
    }

    public function xmlImpuestos($xml, $Impuestos){

        $impuestos = json_decode(json_encode($Impuestos, true));

        $nodoImpuestos = $xml->createElement('cfdi:Impuestos');
            $nodoImpuestos->setAttribute('TotalImpuestosRetenidos', $impuestos->TotalImpuestosRetenidos);
            $nodoImpuestos->setAttribute('TotalImpuestosTrasladados', $impuestos->TotalImpuestosTrasladados);

            if (isset($impuestos->Traslados)) {
                $nodoTraslados = $xml->createElement('cfdi:Traslados');
                    foreach($impuestos->Traslados as $key => $traslado){
                        $nodoTraslado = $xml->createElement('cfdi:Traslado');
                            $nodoTraslado->setAttribute('Impuesto', $traslado->Impuesto);
                            $nodoTraslado->setAttribute('TipoFactor', $traslado->TipoFactor);
                            $nodoTraslado->setAttribute('TasaOCuota', $traslado->TasaOCuota);
                            $nodoTraslado->setAttribute('Importe', $traslado->Importe);
                            $nodoTraslados->appendChild($nodoTraslado);
                    }
                $nodoImpuestos->appendChild($nodoTraslados);
            }
            if (isset($impuestos->Retenciones)) {
                $nodoRetenciones = $xml->createElement('cfdi:Retenciones');
                    foreach($impuestos->Retenciones as $key => $retencion){
                        $nodoRetencion = $xml->createElement('cfdi:Retencion');
                            $nodoRetencion->setAttribute('Impuesto', $retencion->Impuesto);
                            $nodoRetencion->setAttribute('TipoFactor', $retencion->TipoFactor);
                            $nodoRetencion->setAttribute('TasaOCuota', $retencion->TasaOCuota);
                            $nodoRetencion->setAttribute('Importe', $retencion->Importe);
                        $nodoRetenciones->appendChild($nodoRetencion);
                    }
                $nodoImpuestos->appendChild($nodoRetenciones);
            }

        $getNodoComprobante = $xml->firstChild;
        $getNodoComprobante->appendChild($nodoImpuestos);
        return $xml;
    }

    public function xmlComplementoPago($xml, $Pago, $DoctoRelacionado){

        $pago = json_decode(json_encode($Pago, true));

        $doctoRelacionado = json_decode(json_encode($DoctoRelacionado, true));
    
        $nodoComplemento = $xml->createElement('cfdi:Complemento');

        $nodoPagos = $xml->createElement('pago10:Pagos');
        $nodoPagos->setAttribute('Version', '1.0');

        $nodoPago = $xml->createElement('pago10:Pago');
            $nodoPago->setAttribute('FechaPago', $pago->FechaPago);
            $nodoPago->setAttribute('FormaDePagoP', $pago->FormaDePagoP);
            $nodoPago->setAttribute('MonedaP', $pago->MonedaP);
        if($pago->MonedaP != "MXN"){
            $nodoPago->setAttribute('TipoCambioP', '');
        }
        $nodoPago->setAttribute('Monto', $pago->Monto);
        //$nodoPago->setAttribute('NumOperacion', '');

        foreach ($doctoRelacionado as $key => $docto) {
            $nodoDoctoRelacionado = $xml->createElement('pago10:DoctoRelacionado');
            $nodoDoctoRelacionado->setAttribute('IdDocumento', $docto->IdDocumento);
            $nodoDoctoRelacionado->setAttribute('Folio', $docto->Folio);
            $nodoDoctoRelacionado->setAttribute('MonedaDR', $docto->MonedaDR);
            $nodoDoctoRelacionado->setAttribute('MetodoDePagoDR', 'PPD');
            $nodoDoctoRelacionado->setAttribute('NumParcialidad', $docto->NumParcialidad);
            $nodoDoctoRelacionado->setAttribute('ImpSaldoAnt', $docto->ImpSaldoAnt);
            $nodoDoctoRelacionado->setAttribute('ImpPagado', $docto->ImpPagado);
            $nodoDoctoRelacionado->setAttribute('ImpSaldoInsoluto', $docto->ImpSaldoInsoluto);
            $nodoPago->appendChild($nodoDoctoRelacionado);
        }

        $nodoPagos->appendChild($nodoPago);
        $nodoComplemento->appendChild($nodoPagos);
        $getNodoComprobante = $xml->firstChild;
        $getNodoComprobante->appendChild($nodoComplemento);

        return $xml;
    }

    public function cadenaOriginalYSello($xml, $Emisor){

        $emisor = json_decode(json_encode($Emisor, true));
        $genCadenaOriginal = new GenCadenaOriginal();
        $xmlString = trim($xml->saveXML());
        $cadena = $genCadenaOriginal->cadenaOriginal($xmlString);

        error_log($cadena);

        $sello = $this->Sello($cadena, $emisor->Rfc);

        $getNodoComprobante = $xml->firstChild;
        $getNodoComprobante->setAttribute('Sello', $sello);
        
        return $xml;
    }

    public function cadenaOriginalCompPago($xml, $Comprobante,$serialNumber,$Emisor,$Receptor,$Pago,$DoctoRelacionado){

        $cadena = "||3.3|";
        $compComprobante = $serialNumber."|0|XXX|0|P|";
        $concepto = "84111506|1|ACT|Pago|0|0|1.0|";

        $comprobante = json_decode(json_encode($Comprobante, true));
        $cadena .= $comprobante->Serie."|".$comprobante->Folio."|".$comprobante->Fecha."|".$compComprobante.$comprobante->LugarExpedicion."|";
        
        $emisor = json_decode(json_encode($Emisor, true));
        $cadena .= $emisor->Rfc."|".$emisor->Nombre."|".$emisor->RegimenFiscal."|";

        $receptor = json_decode(json_encode($Receptor, true));
        $cadena .= $receptor->Rfc."|".$receptor->Nombre."|".$receptor->UsoCFDI."|";

        $cadena .= $concepto;

        $pago = json_decode(json_encode($Pago, true));
        $cadena .= $pago->FechaPago."|".$pago->FormaDePagoP."|".$pago->MonedaP."|".$pago->Monto."|";

        $doctoRelacionado = json_decode(json_encode($DoctoRelacionado, true));

        foreach ($doctoRelacionado as $key => $docto) {
            $cadena .= $docto->IdDocumento."|".$docto->Folio."|".$docto->MonedaDR."|".$docto->MetodoDePagoDR."|".$docto->NumParcialidad."|".$docto->ImpSaldoAnt."|".$docto->ImpPagado."|".$docto->ImpSaldoInsoluto."|";
        }

        $cadena .= "|";

        //error_log($cadena);

        $sello = $this->Sello($cadena, $emisor->Rfc);

        $getNodoComprobante = $xml->firstChild;
        $getNodoComprobante->setAttribute('Sello', $sello);
            
        return $xml;
    }

    public function xmlTimbreFiscal($xml, $TimbreFiscalDigital){

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

        $getNodoComplemento = $xml->getElementsByTagName('cfdi:Complemento')->item(0);
        $getNodoComplemento->appendChild($nodoTimbreFiscal);

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
