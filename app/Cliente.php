<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $rfc
 * @property string $uso_cfdi
 * @property string $tipo
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Factura[] $facturas
 * @property FisicasCliente[] $fisicasClientes
 * @property NombresComercialesCliente[] $nombresComercialesClientes
 * @property PersonasHasCliente[] $personasHasClientes
 */
class Cliente extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['rfc', 'uso_cfdi', 'tipo', 'deleted_at', 'created_at', 'updated_at'];

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
    public function fisicasClientes()
    {
        return $this->hasMany('App\FisicasCliente');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function nombresComercialesClientes()
    {
        return $this->hasMany('App\NombresComercialesCliente');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function personasHasClientes()
    {
        return $this->hasMany('App\PersonasHasCliente');
    }
}
