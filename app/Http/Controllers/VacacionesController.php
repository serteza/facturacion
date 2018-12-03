<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\CarbonPeriod;
use Carbon\Carbon;
use App\Empleado;
use App\MovPuestos;
use App\Integrado;
use App\VacacionesAcum;
use App\SolicitudVacaciones;
use App\UserSession;
use App\Permiso;
use App\User;

use DB;

class VacacionesController extends Controller
{
    public function search_empleados(Request $req){
        $this->validate($req, [
            'q' => 'required|string|min:1|regex:/^[\pL\s\-]+$/u'
        ]);
         $q = $req->query();

         $user = User::select('UsuarioID','TipoUsuario', 'CodigoJefeInmediato')->where('UsuarioID', $q['user'])->first();

         //return response()->json(['empleados'=>$user],200);
         if($user->TipoUsuario == 1){

            $q_found = Empleado::select('Codigo', 'MovID', 'ApellidoPaterno', 'ApellidoMaterno', 'Nombre')
            ->where('JefeInmediatoID', $user->CodigoJefeInmediato)
            ->where('Estatus', 'A')
            ->where(DB::raw('CONCAT(ApellidoPaterno," ", ApellidoMaterno," ", Nombre)'), 'LIKE', '%' . $q['q'] . '%')
            ->orderBy('ApellidoPaterno', 'asc')
            ->orderBy('ApellidoMaterno', 'asc')
            ->orderBy('Nombre', 'asc')
            ->take(10)->get();

        
                if($q_found == null){
                    return response()->json(['empleados'=>'No se encontró ningún emplado...'],200);
                }else{
                    
                    return response()->json(['empleados'=>$q_found],200);
                } 
         }else{

            $userAgrupacionID = Permiso::select('AgrupacionID')->where("UsuarioID",$q['user'])->distinct()->get();
            $userAreaID = Permiso::select('AreaID')->where("UsuarioID",$q['user'])->distinct()->get();
            $userDepartamentoID = Permiso::select('DepartamentoID')->where("UsuarioID",$q['user'])->distinct()->get();

            //return response()->json(['empleados'=>$userAreaID],200);
            
            $q_found = Empleado::with(['movPuesto.agrupacion','movPuesto.area','movPuesto.departamento'])
            ->whereHas('movPuesto',function ($query) use ($userAgrupacionID, $userAreaID, $userDepartamentoID) {

                $query->whereHas('agrupacion',function ($query) use ($userAgrupacionID){
                    $query->whereIn('AgrupacionID', $userAgrupacionID->toArray());
                    
                })
                ->whereHas('area', function ($query) use ($userAreaID){
                    $query->whereIn('AreaID', $userAreaID->toArray());
                    
                })
                ->whereHas('departamento', function ($query) use ($userDepartamentoID){
                    $query->whereIn('DepartamentoID', $userDepartamentoID->toArray());
                });
            })
            ->select('Codigo', 'MovID', 'ApellidoPaterno', 'ApellidoMaterno', 'Nombre')
            ->where('Estatus', 'A')
            ->where(DB::raw('CONCAT(ApellidoPaterno," ", ApellidoMaterno," ", Nombre)'), 'LIKE', '%' . $q['q'] . '%')
            ->orderBy('ApellidoPaterno', 'asc')
            ->orderBy('ApellidoMaterno', 'asc')
            ->orderBy('Nombre', 'asc')
            ->take(10)->get();

        
                if($q_found == null){
                    return response()->json(['empleados'=>'No se encontró ningún emplado...'],200);
                }else{
                    
                    return response()->json(['empleados'=>$q_found],200);
                }
         }
    }

    public function overtake_vac($id){

        $permitidos = 0;
        $gozados = 0;
        $cancelados = 0;
        $saldo = 0;

        $empleado = Empleado::with('vacaciones')
        ->select('Codigo','FechaAntig','TipoVacaciones')
        ->where('Codigo',$id)->first();

        //si el objeto retorna nulo, no se encuentra en la bd
        if($empleado == null){
            return response()->json(['empleado'=>'No se encontró el empleado...'],200);
        }
        
        //verifica si el arreglo de las vacaciones es 0
        if(sizeof($empleado->vacaciones) == 0){
                $fechaAdlntrAnio = Carbon::parse($empleado->FechaAntig)->addYear()->format('Y-m-d');
                //buscamos en la tabla cat  integrados los tipos de vacaciones
                $diasVac = Integrado::select('VacacionesEspeciales', 'Vacaciones')
                ->where('AnioInicial', 0)->first();
                //validamos que tipo de vacaciones tiene el empleado
                if($empleado->TipoVacaciones == 1){

                    $this->insertarVacaciones($empleado->Codigo, $fechaAdlntrAnio, 1, $diasVac->VacacionesEspeciales,0,0);

                } else{
                    //insertamos las vacaciones del empeado con sus vacaciones especiales
                    $this->insertarVacaciones($empleado->Codigo, $fechaAdlntrAnio, 1, $diasVac->Vacaciones,0,0);

                }
            } else {

                $vacPorDias = Integrado::select('VacacionesEspeciales', 'Vacaciones')
                ->where('AnioInicial', $empleado->vacaciones[0]->num)->first();

                $vacaciones = json_decode(json_encode($empleado->vacaciones, true));

                foreach ($vacaciones as $key => $item) {
                    $permitidos = $permitidos + $item->dias_permitidos;
                    $gozados = $gozados + $item->dias_gozados;
                    $cancelados = $cancelados + $item->dias_cancelados;
                   
                }
                
                $saldo = $saldo + ( $permitidos - ($gozados + $cancelados) );

                if($saldo == 0){
                    //Verifica si tiene vacaciones especiales
                    if($empleado->TipoVacaciones == 1){
                        $this->insertarVacaciones(
                        $empleado->Codigo, //Codigo del empleado
                        Carbon::parse($empleado->vacaciones[0]->anio)->addYear()->format('Y-m-d'), //Año sigiente, depende del ultmimo año creado
                        $empleado->vacaciones[0]->num +1, //Conteo del numero de años 
                        $vacPorDias->VacacionesEspeciales, //días permitidos
                        0, //días gozados
                        0); //días cancelados

                    }else {

                        $this->insertarVacaciones(
                            $empleado->Codigo, //Codigo del empleado
                            Carbon::parse($empleado->vacaciones[0]->anio)->addYear()->format('Y-m-d'), //Año sigiente, depende del ultmimo año creado
                            $empleado->vacaciones[0]->num +1, //Conteo del numero de años 
                            $vacPorDias->Vacaciones, //días permitidos
                            0, //días gozados
                            0); //días cancelados

                    }


                
                } else {

                    return response()->json(['empleado'=>'Tienes días restantes..'],200);

                }
            }

            $empleado_found = Empleado::with(['movPuesto.area','movPuesto.departamento', 'movPuesto.puesto', 'vacaciones.solicitudes'])
            ->select('MovID','Codigo','ApellidoPaterno','ApellidoMaterno','Nombre','Telefono','FechaIngreso','FechaAntig','TipoVacaciones')
            ->where('Codigo',$id)->first();

            $vac_acum = SolicitudVacaciones::select('uuid','FechaSolicitud','FechaInicio','FechaTermino','FechaRegreso','Observaciones', 'ImgFormato','Cancelado')
            ->where('Codigo', $empleado_found->Codigo)
            ->groupBy('uuid')->get();
            $arrayOut = json_decode(json_encode($vac_acum, true));

             foreach ($arrayOut as $key => $item) {
                $transac = SolicitudVacaciones::with('solicitudes')
                ->select('SolicitudID', 'acum_vacaciones_id')
                ->where('uuid',$item->uuid)->get();
                
                $item->solicitudes = $transac;
            }
            $empleado_found->transacciones = $arrayOut;

            return response()->json(['empleado' => $empleado_found] ,200);

    }

    public function get_empleado($id){
        //error_log(uniqid());
        //$dates = array();
        //sumartoria de dias de vacaciones
        $diasTotales = 0;
        //consulta del empleado para saber si tiene registro en la tabla acum_vacaciones
        $empleado = Empleado::with('vacaciones')
        ->select('Codigo','FechaAntig','TipoVacaciones')
        ->where('Codigo',$id)->first();
        //si el objeto retorna nulo, no se encuentra en la bd
        if($empleado == null){
            return response()->json(['empleado'=>'No se encontró el empleado...'],200);
        }
        $fechaPrimerAnio = Carbon::parse($empleado->FechaAntig)->addYear()->format('Y-m-d');
        //Genera periodos por año desde su fecha de antigtuedad hasta la fecha actual
        $period = CarbonPeriod::create($fechaPrimerAnio, '1 year', Carbon::now()->toDateString());

        //verifica si el arreglo de las vacaciones es 0
        if(sizeof($empleado->vacaciones) == 0){
            //recorrremos el periodo creado para darle formato a las fechas creadas
            foreach ($period as $key => $date) {
                //buscamos en la tabla cat  integrados los tipos de vacaciones
                $diasVac = Integrado::select('VacacionesEspeciales', 'Vacaciones')
                ->where('AnioInicial', $key)->first();
                //validamos que tipo de vacaciones tiene el empleado
                if($empleado->TipoVacaciones == 1){

                    //insertamos las vacaciones del empeado con sus vacaciones especiales
                    $this->insertarVacaciones($empleado->Codigo,$date->format('Y-m-d'), $key + 1,$diasVac->VacacionesEspeciales,0,0);
                    //sumamos al total de dias
                    $diasTotales += $diasVac->VacacionesEspeciales;
                } else{
                    //insertamos las vacaciones del empeado con sus vacaciones especiales
                    $this->insertarVacaciones($empleado->Codigo,$date->format('Y-m-d'), $key + 1,$diasVac->Vacaciones,0,0);
                    //sumamos al total de dias
                    $diasTotales += $diasVac->Vacaciones;
                }
            }
        //validamos si el array del empleado ya contiene registros, pero es menor al periodo generado anteriormente
        //entonces existen nuevas vacaciones  y las insertamos  
        }else{
            $periodArrayIn = $period->toArray();
            if (sizeof($empleado->vacaciones) < sizeof($periodArrayIn)) {
                
                $periodArrayOut = array_slice($periodArrayIn, sizeof($empleado->vacaciones));
                $periodArrayOut = json_decode(json_encode($periodArrayOut, true));

                foreach ($periodArrayOut as $key => $item) {
                    $date = Carbon::parse($item->date)->format('Y-m-d');

                    $diasVac = Integrado::select('VacacionesEspeciales', 'Vacaciones')
                    ->where('AnioInicial', $key +sizeof($empleado->vacaciones))->first();

                    if($empleado->TipoVacaciones == 1){

                        //insertamos las vacaciones del empeado con sus vacaciones especiales
                        $this->insertarVacaciones($empleado->Codigo, $date, $key + (sizeof($empleado->vacaciones)+1) ,$diasVac->VacacionesEspeciales,0,0);
                        //sumamos al total de dias
                        $diasTotales += $diasVac->VacacionesEspeciales;
                    } else{

                        //insertamos las vacaciones del empeado con sus vacaciones especiales
                        $this->insertarVacaciones($empleado->Codigo, $date, $key + (sizeof($empleado->vacaciones)+1),$diasVac->Vacaciones,0,0);
                        //sumamos al total de dias
                        $diasTotales += $diasVac->Vacaciones;
                    }
                    
                }
            }
        }

        //Buscamos al mismo empleado pero ya con la informacion 
        $empleado_found = Empleado::with(['movPuesto.area','movPuesto.departamento', 'movPuesto.puesto', 'vacaciones.solicitudes'])
        ->select('MovID','Codigo','ApellidoPaterno','ApellidoMaterno','Nombre','Telefono','FechaIngreso','FechaAntig','TipoVacaciones')
        ->where('Codigo',$id)->first();

        //$totalYears = sizeof($dates);

        //si el objeto retorna nulo, no se encuentra en la tabla
        if($empleado_found == null){
            return response()->json(['empleado'=>'No se encontró el empleado...'],200);
            
        }else{
            
            $empleado = $this->empleado_response($empleado_found);

            return response()->json(['empleado'=> $empleado],200);
           
        }

    }

    public function update_url(Request $req, $uuid){

        if($req->has('link')){

            SolicitudVacaciones::where('uuid',$uuid)->update(['ImgFormato' => $req->link]);

            $transac = SolicitudVacaciones::select('Codigo')
            ->where('uuid',$uuid)->first();
    
            $empleado_found = Empleado::with(['movPuesto.area','movPuesto.departamento', 'movPuesto.puesto', 'vacaciones.solicitudes'])
            ->select('MovID','Codigo','ApellidoPaterno','ApellidoMaterno','Nombre','Telefono','FechaIngreso','FechaAntig','TipoVacaciones')
            ->where('Codigo',$transac->Codigo)->first();
    
            $empleado = $this->empleado_response($empleado_found);
            
    
            return response()->json(['empleado'=> $empleado],200);

        }else{

            return response()->json(['empleado'=> "No se envió la url"],200);   
        }
    }

    public function update_status(Request $req ,$uuid){

        $transac = SolicitudVacaciones::with('solicitudes')
        ->select('SolicitudID', 'acum_vacaciones_id','NumeroDias','Cancelado')
        ->where('uuid',$uuid)->get();

        //$toUpdate = SolicitudVacaciones::where('uuid',$uuid);
    
        $arrTransac = collect(json_decode(json_encode($transac, true)));

        // si status viene en 2
        if($req->status == 2){

            foreach ($arrTransac as $key => $item) {

                if($item->Cancelado == 0 || $item->Cancelado == 1){
                    SolicitudVacaciones::where('uuid',$uuid)->update(['Cancelado' => 2]);
                    VacacionesAcum::where('id', $item->solicitudes->id)
                    ->update(['dias_gozados' => $item->solicitudes->dias_gozados - $item->NumeroDias]);
                }
            }
        }else if($req->status == 1 || $req->status == 0) {

            foreach ($arrTransac as $key => $item) {

                if($item->Cancelado == 2){
                    //SolicitudVacaciones::where('uuid',$uuid)->update(['Cancelado' => $req->status]);
                    VacacionesAcum::where('id', $item->solicitudes->id)
                    ->update(['dias_gozados' => $item->solicitudes->dias_gozados + $item->NumeroDias]);
                }
                SolicitudVacaciones::where('uuid',$uuid)->update(['Cancelado' => $req->status]);
            }
        }

        $transac = SolicitudVacaciones::select('Codigo')
        ->where('uuid',$uuid)->first();

        $empleado_found = Empleado::with(['movPuesto.area','movPuesto.departamento', 'movPuesto.puesto', 'vacaciones.solicitudes'])
        ->select('MovID','Codigo','ApellidoPaterno','ApellidoMaterno','Nombre','Telefono','FechaIngreso','FechaAntig','TipoVacaciones')
        ->where('Codigo',$transac->Codigo)->first();

        $empleado = $this->empleado_response($empleado_found);
        
        return response()->json(['empleado'=> $empleado],200);
    }

    public function create_solicitudes_vac(Request $req){

       /* $this->validate($req, [
            'codigo_id' => 'required|numeric|min:1',
            'fecha_solicitud' => 'required|date',
            'numero_dias' => 'required|numeric|min:1',
            'fecha_inicio' => 'required|date',
            'fecha_termino' => 'required|date',
            'fecha_regreso' => 'required|date',
            'observaciones' => 'required|string|min:1|regex:/^[\pL\s\-]+$/u',
            'user' => 'required|numeric'
        ]);*/
        $numero_dias = intval($req->numero_dias);
        $uuid = uniqid();
        //error_log($uuid);
        $found_vac_acum = VacacionesAcum::where('codigo_id', $req->codigo_id)->get();
    
        if($found_vac_acum == null){
            
            return response()->json(['solicitud'=>'No se encontró el periodo vacacional solicitado ó el periodo selecionado no es del empleado...'],200);
        
        } else {
            $Arrayfound_vac_acum = json_decode(json_encode($found_vac_acum, true));
            foreach ($Arrayfound_vac_acum as $key => $value) {
                //error_log('antes: '.$numero_dias);
                if($numero_dias != 0){
                    if($value->dias_cancelados == 0 && $value->dias_permitidos > $value->dias_gozados){
                
                        $resta_dias = $value->dias_permitidos - $value->dias_gozados;
                        //error_log('resta dias: '.$resta_dias);
                        if($resta_dias < $numero_dias){

                            $this->create_and_update_sol(0,
                                                         $value->id,
                                                         $uuid, 
                                                         $value->dias_gozados, 
                                                         $resta_dias,
                                                         $req->codigo_id, 
                                                         Carbon::parse($req->fecha_solicitud)->format('Y-m-d'), 
                                                         Carbon::parse($req->fecha_inicio)->format('Y-m-d'), 
                                                         Carbon::parse($req->fecha_termino)->format('Y-m-d'), 
                                                         Carbon::parse($req->fecha_regreso)->format('Y-m-d'), 
                                                         $req->observaciones, 
                                                         $req->user);
                            error_log('true num dias: '.$numero_dias);
                            error_log('true resta dias: '.$resta_dias);
                            $numero_dias =  $numero_dias - $resta_dias;
                            error_log('true: '.$numero_dias);
                            
                        }else{
                            $this->create_and_update_sol(0,
                                                         $value->id,
                                                         $uuid, 
                                                         $value->dias_gozados, 
                                                         $numero_dias,
                                                         $req->codigo_id,  
                                                         Carbon::parse($req->fecha_solicitud)->format('Y-m-d'), 
                                                         Carbon::parse($req->fecha_inicio)->format('Y-m-d'), 
                                                         Carbon::parse($req->fecha_termino)->format('Y-m-d'), 
                                                         Carbon::parse($req->fecha_regreso)->format('Y-m-d'), 
                                                         $req->observaciones, 
                                                         $req->user);
                            error_log('false num dias: '.$numero_dias);
                            error_log('false resta dias: '.$resta_dias);
                            $numero_dias = $numero_dias - $numero_dias;
                            error_log('false: '.$numero_dias);
                        }

                    }
                }
            }

            $empleado_found = Empleado::with(['movPuesto.area','movPuesto.departamento', 'movPuesto.puesto', 'vacaciones.solicitudes'])
            ->select('MovID','Codigo','ApellidoPaterno','ApellidoMaterno','Nombre','Telefono','FechaIngreso','FechaAntig','TipoVacaciones')
            ->where('Codigo',$req->codigo_id)->first();

            $vac_acum = SolicitudVacaciones::select('uuid','FechaSolicitud','FechaInicio','FechaTermino','FechaRegreso','Observaciones', 'ImgFormato','Cancelado')
            ->where('Codigo', $req->codigo_id)
            ->groupBy('uuid')->get();
            $arrayOut = json_decode(json_encode($vac_acum, true));

            
            foreach ($arrayOut as $key => $item) {

                $numDiasSolicitados =0;
                $periodos =[];
                
                $transac = SolicitudVacaciones::with(['solicitudes'=> function($query) {
                    $query->select('id','anio');
                }])
                ->select('acum_vacaciones_id','NumeroDias')
                ->where('uuid',$item->uuid)->get();
           
             
                $arrayAcum = json_decode(json_encode($transac, true));
                foreach ($arrayAcum as $key => $itemAcum) {
                    //$periodo->anio = $itemAcum->anio;
                
                    $periodos[] = [
                        'anio' => $itemAcum->solicitudes->anio,
                        'numDias' => $itemAcum->NumeroDias
                    ];
                    $numDiasSolicitados += $itemAcum->NumeroDias;
                }

                $pdf=[
                'FechaSolicitud' => $item->FechaSolicitud,
                'Nombre' => $empleado_found->Nombre,
                'ApellidoPaterno' => $empleado_found->ApellidoPaterno,
                'ApellidoMaterno' => $empleado_found->ApellidoMaterno,
                'Area' => $empleado_found->movPuesto->area->Area,
                'Departamento' => $empleado_found->movPuesto->departamento->Departamento,
                'Puesto' => $empleado_found->movPuesto->puesto->Puesto,
                'Telefono' => $empleado_found->Telefono,
                'FechaInicio' => $item->FechaInicio,
                'FechaTermino' => $item->FechaTermino,
                'FechaRegreso' => $item->FechaRegreso,
                'Observaciones' => $item->Observaciones,
                'periodos' => $periodos
                ];
                $item->pdf = $pdf;
                $item->numDiasSolicitados = $numDiasSolicitados;
            }

            $empleado_found->transacciones = $arrayOut;


            $periodos =[];
                
            $transacActual = SolicitudVacaciones::with(['solicitudes'=> function($query) {
                $query->select('id','anio');
            }])
            ->select('acum_vacaciones_id','FechaSolicitud','FechaInicio','FechaTermino','FechaRegreso','Observaciones','NumeroDias')
            ->where('uuid',$uuid)->get();
       
         
            $arrayAcumActual = json_decode(json_encode($transacActual, true));
            foreach ($arrayAcumActual as $key => $itemAcumActual) {

                $periodos[] = [
                    'anio' => $itemAcumActual->solicitudes->anio,
                    'numDias' => $itemAcumActual->NumeroDias
                ];
                $empleado_found->FechaSolicitud = $itemAcumActual->FechaSolicitud;
                $empleado_found->FechaInicio = $itemAcumActual->FechaInicio;
                $empleado_found->FechaTermino = $itemAcumActual->FechaTermino;
                $empleado_found->FechaRegreso = $itemAcumActual->FechaRegreso;
                $empleado_found->Observaciones = $itemAcumActual->Observaciones;
            }

            $empleado_found->Area = $empleado_found->movPuesto->area->Area;
            $empleado_found->Departamento = $empleado_found->movPuesto->departamento->Departamento;
            $empleado_found->Puesto = $empleado_found->movPuesto->puesto->Puesto;

            unset($empleado_found->movPuesto);
            
            $empleado_found->periodos = $periodos;
            $empleado_found->uuid = $uuid;

            return response()->json(['solicitud'=> $empleado_found],200);
        }
    }

    public function update_solicitud_vac(Request $req, $id){

        $this->validate($req, [
            'fecha_solicitud' => 'date',
            'numero_dias' => 'numeric|min:0',
            'fecha_inicio' => 'date',
            'fecha_termino' => 'date',
            'fecha_regreso' => 'date',
            'observaciones' => 'string|min:1|regex:/^[\pL\s\-]+$/u',
            'user' => 'required|numeric'
        ]);

        $foundSolicitud = SolicitudVacaciones::where('SolicitudID', $id)->first();
        
        if($foundSolicitud == null){
            return response()->json(['solicitud'=> 'No se encontró la solicitud...'],200); 
        } else if($req->has('numero_dias')){

            $foundSolicitud->solicitudes()
            ->update(['dias_gozados' => $req->numero_dias]);

            $foundSolicitud->update(['NumeroDias' => $req->numero_dias,
                                     'FechaInicio' => $req->fecha_inicio,
                                     'FechaTermino' => $req->fecha_termino,
                                     'FechaRegreso'=> $req->fecha_regreso,
                                     'Observaciones' => $req->observaciones]);

            $solicitudUp = SolicitudVacaciones::with('solicitudes')
            ->where('SolicitudID', $foundSolicitud->SolicitudID)->first();

            return response()->json(['solicitud'=> $solicitudUp],200);
        }

    }

    public function cancel_vac($id){
        $cancelacion = VacacionesAcum::where('id', $id)->first();
        $uuid = uniqid();
        if($cancelacion->dias_cancelados != 0){
            return response()->json(['solicitud'=> "Ya han sido canceladas"],200);
        }else{
            $diasCancelados = $cancelacion->dias_permitidos - $cancelacion->dias_gozados;

            $this->create_and_update_sol(1,
                                        $cancelacion->id,
                                        $uuid, 
                                        0, 
                                        $diasCancelados,
                                        $cancelacion->codigo_id, 
                                        Carbon::now()->toDateString(), 
                                        Carbon::now()->toDateString(), 
                                        Carbon::now()->toDateString(),
                                        Carbon::now()->toDateString(), 
                                        "Cancelación de días restantes", 
                                        0);

            $cancelacion->update(["dias_cancelados"=> $diasCancelados]);
   
            //$cancelacionUpdated = VacacionesAcum::with('solicitudes')->where('id', $id)->first();

            $empleado_found = Empleado::with(['movPuesto.area','movPuesto.departamento', 'movPuesto.puesto', 'vacaciones.solicitudes'])
            ->select('MovID','Codigo','ApellidoPaterno','ApellidoMaterno','Nombre','Telefono','FechaIngreso','FechaAntig','TipoVacaciones')
            ->where('Codigo',$cancelacion->codigo_id)->first();

            $empleado = $this->empleado_response($empleado_found);
            
            return response()->json(['solicitud'=> $empleado],200);
        }

    }

    public function delete_solicitud_vac($id){

        $solicitudDeleted = SolicitudVacaciones::where('SolicitudID',$id)->first();

        if($solicitudDeleted == null){
            return response()->json(['solicitud'=>'No se encontró el recurso o ya ha sido eliminado...'],200);
            
        }else{
            $solicitudDeleted->delete();
            return response()->json(['solicitud'=>$solicitudDeleted],200);
        }
    }

    //Funcion para insertar vacaciones
    protected function insertarVacaciones($codigo,$anio,$num,$diasPermitidos,$diasGozados,$diasCancelados){
            $newVac = new VacacionesAcum;
            $newVac->codigo_id = $codigo;
            $newVac->anio = $anio;
            $newVac->num = $num;
            $newVac->dias_permitidos = $diasPermitidos;
            $newVac->dias_gozados = $diasGozados;
            $newVac->dias_cancelados = $diasCancelados;
            $newVac->save();
    }

    protected function create_and_update_sol($tipo, $id, $uuid ,$dias_gozados, $dias, $codigo_id, $fecha_sol, $fecha_ini, $fecha_ter,$fecha_reg, $obs, $user){
        
        if($tipo == 0){VacacionesAcum::where('id', $id)->update(["dias_gozados" => $dias_gozados + $dias]);}
        
        $newSolicitud = new SolicitudVacaciones;
        $newSolicitud->Codigo = $codigo_id;
        $newSolicitud->acum_vacaciones_id = $id;
        $newSolicitud->uuid = $uuid;
        $newSolicitud->FechaSolicitud = Carbon::parse($fecha_sol)->format('Y-m-d');        
        $newSolicitud->NumeroDias = $dias;
        $newSolicitud->FechaInicio = Carbon::parse($fecha_ini)->format('Y-m-d');
        $newSolicitud->FechaTermino = Carbon::parse($fecha_ter)->format('Y-m-d');
        $newSolicitud->FechaRegreso = Carbon::parse($fecha_reg)->format('Y-m-d');
        $newSolicitud->Observaciones = $obs;
        if($tipo == 1){$newSolicitud->Cancelado = 2;}
        $newSolicitud->CreoUsuarioID = $user;
        $newSolicitud->save();
    }

    protected function empleado_response($empleado_found){

        $vac_acum = SolicitudVacaciones::select('uuid','FechaSolicitud','FechaInicio','FechaTermino','FechaRegreso','Observaciones', 'ImgFormato','Cancelado')
        ->where('Codigo', $empleado_found->Codigo)
        ->groupBy('uuid')->get();
        $arrayOut = json_decode(json_encode($vac_acum, true));
    
         foreach ($arrayOut as $key => $item) {
    
            $numDiasSolicitados =0;
            $periodos =[];
            
            $transac = SolicitudVacaciones::with(['solicitudes'=> function($query) {
                $query->select('id','anio');
            }])
            ->select('acum_vacaciones_id','NumeroDias')
            ->where('uuid',$item->uuid)->get();
       
         
            $arrayAcum = json_decode(json_encode($transac, true));
            foreach ($arrayAcum as $key => $itemAcum) {
                //$periodo->anio = $itemAcum->anio;
            
                $periodos[] = [
                    'anio' => $itemAcum->solicitudes->anio,
                    'numDias' => $itemAcum->NumeroDias
                ];
                $numDiasSolicitados += $itemAcum->NumeroDias;
            }
    
            $pdf=[
            'FechaSolicitud' => $item->FechaSolicitud,
            'Nombre' => $empleado_found->Nombre,
            'ApellidoPaterno' => $empleado_found->ApellidoPaterno,
            'ApellidoMaterno' => $empleado_found->ApellidoMaterno,
            'Area' => $empleado_found->movPuesto->area->Area,
            'Departamento' => $empleado_found->movPuesto->departamento->Departamento,
            'Puesto' => $empleado_found->movPuesto->puesto->Puesto,
            'Telefono' => $empleado_found->Telefono,
            'FechaInicio' => $item->FechaInicio,
            'FechaTermino' => $item->FechaTermino,
            'FechaRegreso' => $item->FechaRegreso,
            'Observaciones' => $item->Observaciones,
            'periodos' => $periodos
            ];
            $item->pdf = $pdf;
            $item->numDiasSolicitados = $numDiasSolicitados;
        }
    
        $empleado_found->Area = $empleado_found->movPuesto->area->Area;
        $empleado_found->Departamento = $empleado_found->movPuesto->departamento->Departamento;
        $empleado_found->Puesto = $empleado_found->movPuesto->puesto->Puesto;
    
        unset($empleado_found->movPuesto);
    
        $empleado_found->transacciones = $arrayOut;
        
         //(object) ["vacaciones" =>$dates, "años_totales" => $totalYears, "dias_totales" => $diasTotales];
        return  $empleado_found;
    
    }
}
