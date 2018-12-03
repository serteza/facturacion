<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VacacionesAcum extends Model
{
    protected $table = 'acum_vacaciones';
    
    protected $primaryKey = 'id';

    protected $fillable = [
        'anio',
        'dias_permitidos',
        'dias_gozados',
        'dias_cancelados'
    ];

    protected $hidden = [
        'codigo_id',
        'created_at',
        'updated_at'
    ];

    public  function empleado()
    {
        return $this->belongsTo('App\Empleado','codigo_id');
    }

    public function solicitudes()
    {
        return $this->hasMany('App\SolicitudVacaciones', 'acum_vacaciones_id');
    }
}
