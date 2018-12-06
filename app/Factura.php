<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $empresa_id
 * @property int $sucursal_id
 * @property int $cliente_id
 * @property string $neto
 * @property string $serie
 * @property string $folio
 * @property string $metodo_pago
 * @property string $forma_pago
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Cliente $cliente
 * @property Empresa $empresa
 * @property Sucursal $sucursal
 * @property Cancelacion[] $cancelaciones
 * @property ComplementosPago[] $complementosPagos
 * @property DetallesFactura[] $detallesFacturas
 * @property PendientesCobro[] $pendientesCobros
 * @property TimbradosFactura[] $timbradosFacturas
 */
class Factura extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['empresa_id', 'sucursal_id', 'cliente_id', 'neto', 'serie', 'folio', 'metodo_pago', 'forma_pago', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo('App\Cliente');
    }

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
    public function sucursal()
    {
        return $this->belongsTo('App\Sucursal', 'sucursal_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cancelaciones()
    {
        return $this->hasMany('App\Cancelacion');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function complementosPagos()
    {
        return $this->hasMany('App\ComplementosPago');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detallesFacturas()
    {
        return $this->hasMany('App\DetallesFactura');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pendientesCobros()
    {
        return $this->hasMany('App\PendientesCobro');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function timbradosFacturas()
    {
        return $this->hasMany('App\TimbradosFactura');
    }
}
