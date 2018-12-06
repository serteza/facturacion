<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $factura_id
 * @property int $timbrado_complemento_id
 * @property string $serie
 * @property string $folio
 * @property string $importe
 * @property string $saldo
 * @property string $fecha
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property TimbradosComplemento $timbradosComplemento
 * @property Factura $factura
 */
class ComplementosPago extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['factura_id', 'timbrado_complemento_id', 'serie', 'folio', 'importe', 'saldo', 'fecha', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function timbradosComplemento()
    {
        return $this->belongsTo('App\TimbradosComplemento', 'timbrado_complemento_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factura()
    {
        return $this->belongsTo('App\Factura');
    }
}
