<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $persona_id
 * @property string $nombre
 * @property string $apellido_paterno
 * @property string $apellido_materno
 * @property string $curp
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Persona $persona
 */
class FisicasPersona extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['persona_id', 'nombre', 'apellido_paterno', 'apellido_materno', 'curp', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function persona()
    {
        return $this->belongsTo('App\Persona');
    }
}
