<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $factura_id
 * @property string $saldo
 * @property string $importe
 * @property string $fecha
 * @property string $forma_pago
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Factura $factura
 * @property TransferenciasCheque[] $transferenciasCheques
 */
class PendientesCobro extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['factura_id', 'saldo', 'importe', 'fecha', 'forma_pago', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factura()
    {
        return $this->belongsTo('App\Factura');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transferenciasCheques()
    {
        return $this->hasMany('App\TransferenciasCheque', 'pendiente_cobro_id');
    }
}
