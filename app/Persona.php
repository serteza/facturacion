<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $contacto_id
 * @property int $origen_id
 * @property string $rfc
 * @property string $residencia_fiscal
 * @property string $tipo
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Origen $origen
 * @property Contacto $contacto
 * @property Empresa[] $empresas
 * @property FisicasPersona[] $fisicasPersonas
 * @property NombresComercialesPersona[] $nombresComercialesPersonas
 * @property PersonasHasCliente[] $personasHasClientes
 */
class Persona extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['contacto_id', 'origen_id', 'rfc', 'residencia_fiscal', 'tipo', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function origen()
    {
        return $this->belongsTo('App\Origen', 'origen_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contacto()
    {
        return $this->belongsTo('App\Contacto');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function empresas()
    {
        return $this->hasMany('App\Empresa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fisicasPersonas()
    {
        return $this->hasMany('App\FisicasPersona');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function nombresComercialesPersonas()
    {
        return $this->hasMany('App\NombresComercialesPersona');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function personasHasClientes()
    {
        return $this->hasMany('App\PersonasHasCliente');
    }
}
