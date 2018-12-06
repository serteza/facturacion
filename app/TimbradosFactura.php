<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $factura_id
 * @property string $serie
 * @property string $folio
 * @property string $folio_fiscal
 * @property string $cadena_original
 * @property string $xml
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Factura $factura
 */
class TimbradosFactura extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['factura_id', 'serie', 'folio', 'folio_fiscal', 'cadena_original', 'xml', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factura()
    {
        return $this->belongsTo('App\Factura');
    }
}
