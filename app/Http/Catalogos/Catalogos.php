<?php

namespace App\Http\Catalogos;

use App\Http\Catalogos\Replace;

class Catalogos {

    public function catalogo($nombreCatalogo, $array, $q){

        $catalogoFilter = array();

        switch ($nombreCatalogo) {
            case "c_Aduana":
                $catalogoFilter = $this->filterArray($q, $array, "basic");
                break;
            case "c_ClaveProdServ":
                $catalogoFilter = $this->filterArray($q, $array, $nombreCatalogo);
                break;
            case "c_ClaveUnidad":
                $catalogoFilter = $this->filterArray($q, $array, $nombreCatalogo);
                break;
            case "c_CodigoPostal":
                $catalogoFilter = $this->filterArray($q, $array, $nombreCatalogo);
                break;
            case "c_FormaPago":
                $catalogoFilter = $this->filterArray($q, $array, "basic");
                break;
            case "c_Impuesto":
                $catalogoFilter = $this->filterArray($q, $array, "basic");
                break;
            case "c_MetodoPago":
                $catalogoFilter = $this->filterArray($q, $array, "basic");
                break;
            case "c_Moneda":
                $catalogoFilter = $this->filterArray($q, $array, "basic");
                break;
            case "c_NumPedimentoAduana":
                $catalogoFilter = $this->filterArray($q, $array, $nombreCatalogo);
                break;
            case "c_Pais":
                $catalogoFilter = $this->filterArray($q, $array, "basic");
                break;
            case "c_PatenteAduanal":
                $catalogoFilter = $this->filterArray($q, $array, $nombreCatalogo);
                break;
            case "c_RegimenFiscal":
                $catalogoFilter = $this->filterArray($q, $array, "basic");
                break;
            case "c_TasaOCuota":
                $catalogoFilter = $this->filterArray($q, $array, $nombreCatalogo);
                break;
            case "c_TipoDeComprobante":
                $catalogoFilter = $this->filterArray($q, $array, "basic");
                break;
            case "c_TipoFactor":
                $catalogoFilter = $array;
                break;
            case "c_TipoRelacion":
                $catalogoFilter = $this->filterArray($q, $array, "basic");
                break;
            case "c_UsoCFDI":
                $catalogoFilter = $this->filterArray($q, $array, "basic");
                break;
            default:
                $catalogoFilter = $array;
        }

        return $catalogoFilter ;

    }

    private function filterArray($word, $array, $cat){

        $newArray = array();

        $newString = new Replace();

        switch ($cat) {

            case 'c_ClaveProdServ':

                foreach ($array as $key => $item) {
           
                    if (stripos( strtolower($item->id), strtolower($word)) !== false ||
                        stripos( $newString->scann_string( strtolower($item->descripcion)), strtolower($word)) !== false ||
                        stripos( $newString->scann_string( strtolower($item->palabrasSimilares)), strtolower($word)) !== false) {
                        array_push($newArray, $item);
                    }
                }
            
                break;

            case 'c_ClaveUnidad':

                foreach ($array as $key => $item) {
           
                    if (stripos( strtolower($item->id), strtolower($word)) !== false ||
                        stripos( $newString->scann_string( strtolower($item->nombre)), strtolower($word)) !== false ||
                        stripos( $newString->scann_string( strtolower($item->simbolo)), strtolower($word)) !== false) {
                        array_push($newArray, $item);
                    }
                }
            
                break;

            case 'c_CodigoPostal':

                foreach ($array as $key => $item) {
           
                    if (stripos( strtolower($item->id), strtolower($word)) !== false ||
                        stripos( $newString->scann_string( strtolower($item->c_Estado)), strtolower($word)) !== false ||
                        stripos( $newString->scann_string( strtolower($item->c_Municipio)), strtolower($word)) !== false ||
                        stripos( $newString->scann_string( strtolower($item->c_Localidad)), strtolower($word)) !== false) {
                        array_push($newArray, $item);
                    }
                }
            
                break;

            case 'c_NumPedimentoAduana':

                foreach ($array as $key => $item) {
           
                    if (stripos( $newString->scann_string( strtolower($item->c_Aduana)), strtolower($word)) !== false ||
                        stripos( $newString->scann_string( strtolower($item->patente)), strtolower($word)) !== false ||
                        stripos( $newString->scann_string( strtolower($item->ejercicio)), strtolower($word)) !== false) {
                        array_push($newArray, $item);
                    }
                }
            
                break;

            case 'c_PatenteAduanal':

                foreach ($array as $key => $item) {
           
                    if (stripos( $newString->scann_string( strtolower($item->c_PatenteAduanal)), strtolower($word)) !== false ) {
                        array_push($newArray, $item);
                    }
                }
            
                break;

            case 'c_TasaOCuota':

                foreach ($array as $key => $item) {
           
                    if (stripos( $newString->scann_string( strtolower($item->impuesto)), strtolower($word)) !== false ) {
                        array_push($newArray, $item);
                    }
                }
            
                break;
            
            default:

            foreach ($array as $key => $item) {
           
                if (stripos( strtolower($item->id), strtolower($word)) !== false ||
                    stripos( $newString->scann_string( strtolower($item->descripcion)), strtolower($word)) !== false) {
                    array_push($newArray, $item);
                }
            }
            
                break;
        }

        return $newArray;
        
    }

}