<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $empresa_id
 * @property string $nombre
 * @property string $serie
 * @property string $folio
 * @property string $desc_folio
 * @property string $cp
 * @property string $forma_pago
 * @property string $moneda
 * @property string $uso_cfdi
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Empresa $empresa
 * @property Factura[] $facturas
 * @property UsuariosHasSucursale[] $usuariosHasSucursales
 */
class Sucursal extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'sucursales';

    /**
     * @var array
     */
    protected $fillable = ['empresa_id', 'nombre', 'serie', 'folio', 'desc_folio', 'cp', 'forma_pago', 'moneda', 'uso_cfdi', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function empresa()
    {
        return $this->belongsTo('App\Empresa');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function facturas()
    {
        return $this->hasMany('App\Factura', 'sucursal_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usuariosHasSucursales()
    {
        return $this->hasMany('App\UsuariosHasSucursale', 'sucursal_id');
    }
}
