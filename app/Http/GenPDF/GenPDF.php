<?php
namespace App\Http\GenPDF;

class GenPDF {
    
    public function headerComprobante($pdf, $header){

        $header = json_decode(json_encode($header, true));
        $widthHeader=array(55,55);
    
        //titulo
        $pdf->SetFont('Arial','B',15);
        $pdf->Cell('fill',5,"RECEPCION DE PAGOS",0,0,"C");
        $pdf->Ln();
        //Nombre
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell('fill',5,$header->nombre,0,0,"C");
        $pdf->Ln();

        $pdf->Ln();
        $pdf->SetFont("Arial","B",7);
        $pdf->Cell($widthHeader[0],5,"",0,0,"L");

        //logo empresa
        $x =$pdf->GetX();
        $y =$pdf->GetY();
        
        $pdf->Image($header->logo, $x/2.8 , $y, 30, 30, 'JPG');

        $pdf->Cell($widthHeader[1],5,utf8_decode("Fecha y hora de Expedición"),0,0,"L");
        $pdf->Cell($widthHeader[1],5, $header->fechaExp,0,0,"L");
        $pdf->Ln();

        $pdf->Cell($widthHeader[0],5,"",0,0,"L");
        $pdf->Cell($widthHeader[1],5,utf8_decode("Número de Serie de Certificado del Emisor"),0,0,"L");
        $pdf->Cell($widthHeader[1],5, $header->noCertEmisor,0,0,"L");
        $pdf->Ln();
        
        $pdf->Cell($widthHeader[0],5,"",0,0,"L");
        $pdf->Cell($widthHeader[1],5,utf8_decode("Número de Serie de Certificado del SAT"),0,0,"L");
        $pdf->Cell($widthHeader[0],5, $header->noCertSAT,0,0,"L");
        $pdf->Ln();
        
        $pdf->Cell($widthHeader[0],5,"",0,0,"L");
        $pdf->Cell($widthHeader[1],5,utf8_decode("Folio Fiscal"),0,0,"L");
        $pdf->Cell($widthHeader[1],5,$header->uuid,0,0,"L");
        $pdf->Ln();
        
        $pdf->Cell($widthHeader[0],5,"",0,0,"L");
        $pdf->Cell($widthHeader[1],5,utf8_decode("Emitido en"),0,0,"L");
        $pdf->Cell($widthHeader[1],5, $header->cp,0,0,"L");
        $pdf->Ln();
        
        $pdf->Cell($widthHeader[0],5,"",0,0,"L");
        $pdf->Cell($widthHeader[1],5,utf8_decode("Versión del Comprobante"),0,0,"L");
        $pdf->Cell($widthHeader[1],5,"3.3",0,0,"L");
        $pdf->Ln();
        $pdf->SetFont("Arial","B",12);
        $pdf->SetFillColor(213, 216, 220);
        $pdf->SetDrawColor(213, 216, 220);
        $pdf->Ln();

        return $pdf;
    }

    public function datosReceptorFacComprobante($pdf, $datosReceptorFacComp){

        $datosReceptorFacComp = json_decode(json_encode($datosReceptorFacComp, true));

        $pdf->SetFont('Arial','B',8);
        $widthReceptor=array(20,85);
        //header
        $pdf->Cell(105,5,"Datos del Receptor",1,0,"C",true);
        $pdf->Ln();

        $pdf->SetFont('Arial','',7);
        $pdf->Cell($widthReceptor[0],5,"RFC",1,0,"L",false);
        
        $pdf->Cell($widthReceptor[1],5,$datosReceptorFacComp->rfc,1,0,"L",false);
        $pdf->Ln();

        $pdf->Cell($widthReceptor[0],5,"Nombre",1,0,"L",false);
        $pdf->Cell($widthReceptor[1],5, $datosReceptorFacComp->nombreReceptor,1,0,"L",false);
        $pdf->Ln();

        $pdf->Cell($widthReceptor[0],5,utf8_decode("Dirección"),1,0,"L",false);
        $pdf->Cell($widthReceptor[1],5,$datosReceptorFacComp->direccion,1,0,"L",false); 
        $pdf->Ln();

        $pdf->Cell($widthReceptor[0],5,utf8_decode("N° de Cuenta"),1,0,"L",false);
        $pdf->Cell($widthReceptor[1],5,$datosReceptorFacComp->noCuenta,1,0,"L",false);
        $pdf->Ln();
    
        $pdf->Cell($widthReceptor[0],5,"Uso CFDI",1,0,"L",false);
        $pdf->Cell($widthReceptor[1],5,$datosReceptorFacComp->usoCFDI,1,0,"L",false);
        $pdf->Ln();

        $pdf->Cell(105,5,"",1,0,"C",true);
        $x =$pdf->GetX();
        $y =$pdf->GetY();
        
        //header
        $widthReceptor=array(25,55);
        $pdf->SetXY($x + 5, $y - 30);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(80,5,"Datos del Comprobante",1,0,"C",true);
        $pdf->Ln();
        
        $pdf->SetXY($x + 5, $y - 25);
        $pdf->SetFont('Arial','',7);
        $pdf->Cell(25,5,"Serie y Folio",1,0,"L");
        $pdf->Cell(55,5,$datosReceptorFacComp->serieFolio,1,0,"L");
        $pdf->Ln();

        $pdf->SetXY($x + 5, $y - 20);
        $pdf->Cell(25,5,"Cert CSD",1,0,"L");
        $pdf->Cell(55,5, $datosReceptorFacComp->cerCSD,1,0,"L");
        $pdf->Ln();
        
        $pdf->SetXY($x + 5, $y - 15);
        $pdf->Cell(25,5,utf8_decode("Forma de Pago"),1,0,"L");
        $pdf->Cell(55,5,$datosReceptorFacComp->formaPago,1,0,"L"); 
        $pdf->Ln();
        
        $pdf->SetXY($x + 5, $y - 10);
        $pdf->Cell(25,5,utf8_decode("Método de Pago"),1,0,"L");
        $pdf->Cell(55,5,$datosReceptorFacComp->metodoPago,1,0,"L");
        $pdf->Ln();

        $pdf->SetXY($x + 5, $y - 5);    
        $pdf->Cell(25,5,utf8_decode("Tipo de Moneda"),1,0,"L");
        $pdf->Cell(55,5,$datosReceptorFacComp->tipoMoneda,1,0,"L");
        $pdf->Ln();
        
        $pdf->SetXY($x + 5, $y); 
        $pdf->Cell(80,5,"",1,0,"C",true);
        $pdf->Ln();
        $pdf->Ln();
        return $pdf;
    }

    public function datosComp($pdf){

        $doctosRelacionados ='[
            {
                "folioFiscal":"5C11B520-5191-42EC-AE8C-1C7B4E433F3F",
                "folio":"Z 271",
                "impFactura":"$ 1,850.00",
                "impNeto":"$ 1,850.00"
            },
            {
                "folioFiscal":"5C11B520-5191-42EC-AE8C-1C7B4E433F3F",
                "folio":"Z 271",
                "impFactura":"$ 1,850.00",
                "impNeto":"$ 1,850.00"
            }
        ]';

        $doctosRelacionados = json_decode($doctosRelacionados, true);
        
        $doctosRelacionados = json_decode(json_encode($doctosRelacionados, true));

        $width_cell=array(73,31,43,43);
        $pdf->SetFont('Arial','B',8);

        $pdf->SetFillColor(213, 216, 220); // Background color of header 
        // Header starts /// 
        $pdf->Cell($width_cell[0],5,'DOCTO RELACIONADO',1,0,"C",true); // First header column 
        $pdf->Cell($width_cell[1],5,'FOLIO',1,0,"C",true); // Second header column
        $pdf->Cell($width_cell[2],5,'IMPORTE FACTURA',1,0,"C",true); // Third header column 
        $pdf->Cell($width_cell[3],5,'IMPORTE NETO',1,1,"C",true); // Fourth header column
        //// header is over ///////

        $pdf->SetFont('Arial','',7);
        // First row of data
        foreach ($doctosRelacionados as $key => $docto) {
            $pdf->Cell($width_cell[0],5,$docto->folioFiscal,1,0,"C",false); // First column of row 1 
            $pdf->Cell($width_cell[1],5,$docto->folio,1,0,"C",false); // Second column of row 1 
            $pdf->Cell($width_cell[2],5,$docto->impFactura,1,0,"C",false); // Third column of row 1 
            $pdf->Cell($width_cell[3],5,$docto->impNeto,1,1,"C",false); // Fourth column of row 1 
        } 
        $pdf->Ln();
        $pdf->Ln();

        $pdf->Cell(190 ,1,"",1,0,"C",true);

        $pdf->Ln();
        $pdf->Ln();

        return $pdf;
    }

    public function footerComp($pdf, $qr){

        $Sello="dTjdIEtxWazQICFQm++H1ON/FJItp2R476S7bN37Bmm5E0Bc2qRvON6aF2+LeBVEmmJSMgeUGzA7HXEgOxLSn57wGdJysrpbGZt6TIk0+EVWvuHC1UE0Nbjp00ZKUjyBi8LEJfnTo20l3E8yEod9KwZAeuodb10LbcON2mbpEBsAfa2go4DulFwUXfPxCANUUMgVDv6cjztIalErT7vbLlzoGueZzc/DkMTNxic+qghmUSkQLGb8oajS6CFC8zt3wDDKTI6/NdzgOT/8BgEIUaBmzKWu2SlxJKEN+WRaiYWeSxpkQc6HHQLcOdOF2+HtewGzb1zF3eRpEn/e+QjZcw==" ;
        $SelloSAT="kkFHiEVjzYwql7gXbe3P9VAZp3FBFG8Bm3QoCjIaRS5dxemyaHGCNB8Q/4ab7cPjK5+gBRtjBDHB0oc50Wmap5caWwTO0sh9VTD5AjhmhlEhnRllo8JiJVRGP5RGJoyHCtlpAdtDNRszbtJbl4vNBRiMeAeKGN6IuHKU7pTYEo8QTElRSNzjStS7D+1K/zmT+7cSUxavP1yLNTThl4eYVIGX3V0ZzFOzWa/VR/pJU3UfsrGZVEEDAvkakK8VxWK2vogG5D1FHNeSFyynOZR4XktacXtOviMucXoBpX+EHlZRQy+I6DiP8mt7A7S7+EhP1S0bz2pWx6UsBA3vDTaWdw==";
        $RfcProvCertif="DND070112H92";
        $NoCertificadoSAT="00001000000405908583";
        $FechaTimbrado="2018-11-22T18:29:30"; 

        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(190,5,'Sello digital CFDI:',0,0,"L", false);
        $pdf->Ln();
        $pdf->SetFont('Arial','',7);
        $pdf->MultiCell(190 ,3,$Sello,0);
        $pdf->Ln();

        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(190,5,'Sello digital del SAT:',0,0,"L", false);
        $pdf->Ln();
        $pdf->SetFont('Arial','',7);
        $pdf->MultiCell(190 ,3,$SelloSAT.$SelloSAT.$SelloSAT,0);
        $pdf->Ln();

        $x =$pdf->GetX();
        $y =$pdf->GetY();
        $y_pdf =$pdf->GetY();

        $pdf->SetXY($x + 35, $y);        
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(155,5,'Cadena original del complemento de certificacion digital del SAT:',0,0,"L", false);
        $pdf->Ln();
        $pdf->SetXY($x + 35, $y + 5); 
        $pdf->SetFont('Arial','',7);
        $pdf->MultiCell(155 ,3,$SelloSAT.$SelloSAT.$SelloSAT,0);
    
        $y =$pdf->GetY();
        
        
        $pdf->SetXY($x + 35, $y ); 
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(46,5,'RFC del proveedor de certificacion:',0,0,"L", false);
        //$pdf->SetXY($x + 40, $y); 
        $pdf->SetFont('Arial','',7);
        $pdf->Cell(31,5,$RfcProvCertif,0,0,"C", false);

        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(46,5,'Fecha y hora de certificacion:',0,0,"C", false);
        $pdf->SetFont('Arial','',7);
        $pdf->Cell(36,5,$FechaTimbrado,0,0,"C", false);
        $pdf->Ln();

        $y =$pdf->GetY();

        $pdf->SetXY($x + 35, $y ); 
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(46,5,'No. de serie del certificado SAT:',0,0,"L", false);
        $pdf->SetFont('Arial','',7);
        $pdf->Cell(31,5,$NoCertificadoSAT,0,0,"C", false);
        
        //qr del documento
        $pdf->Image($qr, $x , $y_pdf , 30, 30, 'PNG');

        return $pdf;
    }
}