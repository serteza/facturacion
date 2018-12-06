<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $persona_id
 * @property string $giro_empresa
 * @property string $regimen_fiscal
 * @property string $mascara
 * @property string $tag
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Persona $persona
 * @property Factura[] $facturas
 * @property Sucursal[] $sucursales
 * @property UsuariosHasEmpresa[] $usuariosHasEmpresas
 */
class Empresa extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['persona_id', 'giro_empresa', 'regimen_fiscal', 'mascara', 'tag', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function persona()
    {
        return $this->belongsTo('App\Persona');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function facturas()
    {
        return $this->hasMany('App\Factura');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sucursales()
    {
        return $this->hasMany('App\Sucursal');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usuariosHasEmpresas()
    {
        return $this->hasMany('App\UsuariosHasEmpresa');
    }
}
