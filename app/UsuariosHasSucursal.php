<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $usuario_id
 * @property int $sucursal_id
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Sucursal $sucursal
 * @property Usuario $usuario
 */
class UsuariosHasSucursal extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'usuarios_has_sucursales';

    /**
     * @var array
     */
    protected $fillable = ['usuario_id', 'sucursal_id', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sucursal()
    {
        return $this->belongsTo('App\Sucursal', 'sucursal_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo('App\Usuario');
    }
}
