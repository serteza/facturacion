<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function storeKeys(Request $req){

        $dir = $req->rfc;
        $cerFile = $req->key;
        $path = storage_path('app/keys/'.$dir);


        if(is_dir($path)){
            //Storage::disk('local')->put($dir, $cerFile);
            $cerFile->storeAs($path, $dir.".cer");
            return response()->json(["file" => true],200);
        }else{
            mkdir($path, 0755, true);
            //Storage::disk('local')->put($dir, $cerFile);
            $cerFile->storeAs($path, $dir.".cer");
            return response()->json(["file" => false],200);
        }
        //$path = base_path();
    }
}
