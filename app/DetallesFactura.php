<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $factura_id
 * @property string $importe
 * @property string $descuento
 * @property string $iva
 * @property string $ieps
 * @property string $otros_impuestos
 * @property string $moneda
 * @property string $tipo_cambio
 * @property string $uso_cfdi
 * @property string $ine
 * @property string $tipo_comprobante
 * @property string $condiciones_pago
 * @property string $num_cuenta
 * @property string $observaciones
 * @property string $estatus
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Factura $factura
 */
class DetallesFactura extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['factura_id', 'importe', 'descuento', 'iva', 'ieps', 'otros_impuestos', 'moneda', 'tipo_cambio', 'uso_cfdi', 'ine', 'tipo_comprobante', 'condiciones_pago', 'num_cuenta', 'observaciones', 'estatus', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factura()
    {
        return $this->belongsTo('App\Factura');
    }
}
