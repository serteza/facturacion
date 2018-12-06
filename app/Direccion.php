<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $calle
 * @property string $no_interior
 * @property string $no_exterior
 * @property string $cruzaminetos
 * @property string $colonia
 * @property string $cp
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Origen[] $origenes
 */
class Direccion extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'direcciones';

    /**
     * @var array
     */
    protected $fillable = ['calle', 'no_interior', 'no_exterior', 'cruzaminetos', 'colonia', 'cp', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function origenes()
    {
        return $this->hasMany('App\Origen', 'direccion_id');
    }
}
