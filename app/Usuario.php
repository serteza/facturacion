<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $usuario
 * @property int $tipo_usuario
 * @property string $password
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property UsuariosHasEmpresa[] $usuariosHasEmpresas
 * @property UsuariosHasSucursal[] $usuariosHasSucursales
 */
class Usuario extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['usuario', 'tipo_usuario', 'password', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usuariosHasEmpresas()
    {
        return $this->hasMany('App\UsuariosHasEmpresa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usuariosHasSucursales()
    {
        return $this->hasMany('App\UsuariosHasSucursal');
    }
}
