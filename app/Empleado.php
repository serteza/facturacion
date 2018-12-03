<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'nom_catempleados';
    
    protected $primaryKey = 'Codigo';

    protected $hidden = [
        'MovID',
        'TipoVacaciones'
    ];

    public  function movPuesto()
    {
        return $this->belongsTo('App\MovPuestos','MovID');
    }

    public  function vacaciones()
    {
        return $this->hasMany('App\VacacionesAcum','codigo_id')->orderBy('id', 'DESC');
    }

    public  function solicitudVacaciones()
    {
        return $this->hasMany('App\SolicitudVacaciones','Codigo');
    }
}
