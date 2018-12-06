<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $direccion_id
 * @property string $tag
 * @property string $pais
 * @property string $estado
 * @property string $cp_inf
 * @property string $cp_sup
 * @property string $municipio
 * @property string $localidad
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Direccion $direccion
 * @property Persona[] $personas
 */
class Origen extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'origenes';

    /**
     * @var array
     */
    protected $fillable = ['direccion_id', 'tag', 'pais', 'estado', 'cp_inf', 'cp_sup', 'municipio', 'localidad', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function direccion()
    {
        return $this->belongsTo('App\Direccion', 'direccion_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function personas()
    {
        return $this->hasMany('App\Persona', 'origen_id');
    }
}
