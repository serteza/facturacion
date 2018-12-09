<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CatalogosController extends Controller
{
    public function catalogos(Request $req){

        $query = $req->query();

        $cat = $query['cat'];
        $q = "";
        $json = '{}';
        $path = "";

        if($req->has('cat')){
            $cat = $query['cat'];
            if(Storage::disk('catalogos')->exists($cat.".json")){
                $path = storage_path() . "/catalogos/".$cat.".json";
            }else{
                return response()->json(["catalogo" => "No se ha el catálogo '".$cat."' probablemente esté mal escrito"],200);
            }
        }else{
            return response()->json(["catalogo" => "No se ha encontrado la variable 'cat' en la URL"],200);
        }
        
        if($req->has('q')){
            $q = $query['q'];
            $jsonQuery = json_decode(file_get_contents($path),false, 512, JSON_UNESCAPED_UNICODE);

            return response()->json(["catalogo" => gettype($jsonQuery)],200);
        }else{
            $json = json_decode(file_get_contents($path),false, 512, JSON_UNESCAPED_UNICODE);
            return response()->json(["catalogo" => $json],200);
        }
        //$path = storage_path() . "/catalogos/".$cat.".json";
        //$json = json_decode(file_get_contents($path),false, 512, JSON_UNESCAPED_UNICODE);

       
    }
}
