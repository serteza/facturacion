<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CatalogosController extends Controller
{
    public function catalogos(Request $req){

        $q = $req->query();
        
        $path = storage_path() . "/catalogos/".$q['name'].".json";
        $json = json_decode(file_get_contents($path), true);

        return response()->json(["catalogo" => $json],200);
    }
}
