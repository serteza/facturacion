<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\GenCadenaOriginal\GenCadenaOriginal;

class FacturasController extends Controller
{
    public function facturas(Request $req){

        $genCadenaOriginal = new GenCadenaOriginal();

        $xmlString ='<?xml version="1.0" encoding="UTF-8"?>
        <cfdi:Comprobante Version="3.3" Moneda="MXN" TipoCambio="1" LugarExpedicion="64000" Serie="A" Folio="1" Descuento="0.00" MetodoPago="PUE" TipoDeComprobante="I" SubTotal="300.00" Total="342.75" CondicionesDePago="Contado" Certificado="MIIFxTCCA62gAwIBAgIUMjAwMDEwMDAwMDAzMDAwMjI4MTUwDQYJKoZIhvcNAQELBQAwggFmMSAwHgYDVQQDDBdBLkMuIDIgZGUgcHJ1ZWJhcyg0MDk2KTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMSkwJwYJKoZIhvcNAQkBFhphc2lzbmV0QHBydWViYXMuc2F0LmdvYi5teDEmMCQGA1UECQwdQXYuIEhpZGFsZ28gNzcsIENvbC4gR3VlcnJlcm8xDjAMBgNVBBEMBTA2MzAwMQswCQYDVQQGEwJNWDEZMBcGA1UECAwQRGlzdHJpdG8gRmVkZXJhbDESMBAGA1UEBwwJQ295b2Fjw6FuMRUwEwYDVQQtEwxTQVQ5NzA3MDFOTjMxITAfBgkqhkiG9w0BCQIMElJlc3BvbnNhYmxlOiBBQ0RNQTAeFw0xNjEwMjUyMTUyMTFaFw0yMDEwMjUyMTUyMTFaMIGxMRowGAYDVQQDExFDSU5ERU1FWCBTQSBERSBDVjEaMBgGA1UEKRMRQ0lOREVNRVggU0EgREUgQ1YxGjAYBgNVBAoTEUNJTkRFTUVYIFNBIERFIENWMSUwIwYDVQQtExxMQU43MDA4MTczUjUgLyBGVUFCNzcwMTE3QlhBMR4wHAYDVQQFExUgLyBGVUFCNzcwMTE3TURGUk5OMDkxFDASBgNVBAsUC1BydWViYV9DRkRJMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgvvCiCFDFVaYX7xdVRhp/38ULWto/LKDSZy1yrXKpaqFXqERJWF78YHKf3N5GBoXgzwFPuDX+5kvY5wtYNxx/Owu2shNZqFFh6EKsysQMeP5rz6kE1gFYenaPEUP9zj+h0bL3xR5aqoTsqGF24mKBLoiaK44pXBzGzgsxZishVJVM6XbzNJVonEUNbI25DhgWAd86f2aU3BmOH2K1RZx41dtTT56UsszJls4tPFODr/caWuZEuUvLp1M3nj7Dyu88mhD2f+1fA/g7kzcU/1tcpFXF/rIy93APvkU72jwvkrnprzs+SnG81+/F16ahuGsb2EZ88dKHwqxEkwzhMyTbQIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQsFAAOCAgEAJ/xkL8I+fpilZP+9aO8n93+20XxVomLJjeSL+Ng2ErL2GgatpLuN5JknFBkZAhxVIgMaTS23zzk1RLtRaYvH83lBH5E+M+kEjFGp14Fne1iV2Pm3vL4jeLmzHgY1Kf5HmeVrrp4PU7WQg16VpyHaJ/eonPNiEBUjcyQ1iFfkzJmnSJvDGtfQK2TiEolDJApYv0OWdm4is9Bsfi9j6lI9/T6MNZ+/LM2L/t72Vau4r7m94JDEzaO3A0wHAtQ97fjBfBiO5M8AEISAV7eZidIl3iaJJHkQbBYiiW2gikreUZKPUX0HmlnIqqQcBJhWKRu6Nqk6aZBTETLLpGrvF9OArV1JSsbdw/ZH+P88RAt5em5/gjwwtFlNHyiKG5w+UFpaZOK3gZP0su0sa6dlPeQ9EL4JlFkGqQCgSQ+NOsXqaOavgoP5VLykLwuGnwIUnuhBTVeDbzpgrg9LuF5dYp/zs+Y9ScJqe5VMAagLSYTShNtN8luV7LvxF9pgWwZdcM7lUwqJmUddCiZqdngg3vzTactMToG16gZA4CWnMgbU4E+r541+FNMpgAZNvs2CiW/eApfaaQojsZEAHDsDv4L5n3M1CC7fYjE/d61aSng1LaO6T1mh+dEfPvLzp7zyzz+UgWMhi5Cs4pcXx1eic5r7uxPoBwcCTt3YI1jKVVnV7/w=" NoCertificado="30001000000300023708" FormaPago="02" Sello="YNOK3dfsGESVJLRr698YOc1FuhbYXQ4jvKPCp7Pu+Ijy7vo6rQQ6hCzrRg/AJdQmqZUHyZIBJhEVzO5awkQjB/zsy/gjyhDbICJv6bd2kF59M8E2i2yHhcHOTvX52wqI955TvMXf3Adka8NAYwhu6SXbHDnL2VrzMjkflumCtvS9aAnu2Fgw11QMa5MiLowFjInSlMTY5mXv+2MqP+F31UbaH0Ep1qZImxQzBfBsMw6h8YfeQOLGXWUTiA8EHFSccT9sFrfmNGg+MsVPfDEAxLPBwaCCHXjR+aG8J2eGWwz3woA78C60FOccvuMBESrqq6KPk6uIQ8Uy6kHmhk9+eg==" Fecha="2017-08-08T18:02:53" xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:cfdi="http://www.sat.gob.mx/cfd/3">
          <cfdi:Emisor Rfc="LAN7008173R5" Nombre="Empresa de Prueba" RegimenFiscal="601" />
          <cfdi:Receptor Rfc="LAN7008173R5" Nombre="ABASTECEDORA SA DE CV" UsoCFDI="G03" />
          <cfdi:Conceptos>
            <cfdi:Concepto ClaveProdServ="80141706" ClaveUnidad="DAY" Unidad="Día" Cantidad="1" Descripcion="ejemplo" ValorUnitario="150" Importe="150">
              <cfdi:Impuestos>
                <cfdi:Traslados>
                  <cfdi:Traslado Importe="24.00" TasaOCuota="0.160000" TipoFactor="Tasa" Impuesto="002" Base="150" />
                  <cfdi:Traslado Importe="39.75" TasaOCuota="0.265000" TipoFactor="Tasa" Impuesto="003" Base="150" />
                </cfdi:Traslados>
                <cfdi:Retenciones>
                  <cfdi:Retencion Importe="22.50" TasaOCuota="0.150000" TipoFactor="Tasa" Impuesto="001" Base="150" />
                  <cfdi:Retencion Importe="7.50" TasaOCuota="0.050000" TipoFactor="Cuota" Impuesto="003" Base="150" />
                </cfdi:Retenciones>
              </cfdi:Impuestos>
            </cfdi:Concepto>
            <cfdi:Concepto ClaveProdServ="80141706" ClaveUnidad="H87" Unidad="Pieza" Cantidad="1" Descripcion="ejemplo 2" ValorUnitario="150" Importe="150">
              <cfdi:Impuestos>
                <cfdi:Traslados>
                  <cfdi:Traslado Importe="24.00" TasaOCuota="0.160000" TipoFactor="Tasa" Impuesto="002" Base="150" />
                </cfdi:Traslados>
                <cfdi:Retenciones>
                  <cfdi:Retencion Importe="15.00" TasaOCuota="0.100000" TipoFactor="Tasa" Impuesto="001" Base="150" />
                </cfdi:Retenciones>
              </cfdi:Impuestos>
            </cfdi:Concepto>
          </cfdi:Conceptos>
          <cfdi:Impuestos TotalImpuestosRetenidos="45.00" TotalImpuestosTrasladados="87.75">
            <cfdi:Retenciones>
              <cfdi:Retencion Importe="37.50" Impuesto="001" />
              <cfdi:Retencion Importe="7.50" Impuesto="003" />
            </cfdi:Retenciones>
            <cfdi:Traslados>
              <cfdi:Traslado TasaOCuota="0.160000" Importe="48.00" TipoFactor="Tasa" Impuesto="002" />
              <cfdi:Traslado TasaOCuota="0.265000" Importe="39.75" TipoFactor="Tasa" Impuesto="003" />
            </cfdi:Traslados>
          </cfdi:Impuestos>
        </cfdi:Comprobante>'; 

        $xml = new \DOMDocument();

        $xml->loadXML($xmlString);


        $cadena = $genCadenaOriginal->cadenaOriginal($xml);

        return response()->json(["cadena" => $cadena],200);
    }
}
