<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $usuario_id
 * @property int $empresa_id
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Empresa $empresa
 * @property Usuario $usuario
 */
class UsuariosHasEmpresa extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['usuario_id', 'empresa_id', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empresa()
    {
        return $this->belongsTo('App\Empresa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo('App\Usuario');
    }
}
