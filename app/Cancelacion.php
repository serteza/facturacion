<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $factura_id
 * @property string $motivo
 * @property string $fecha
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Factura $factura
 */
class Cancelacion extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'cancelaciones';

    /**
     * @var array
     */
    protected $fillable = ['factura_id', 'motivo', 'fecha', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function factura()
    {
        return $this->belongsTo('App\Factura');
    }
}
