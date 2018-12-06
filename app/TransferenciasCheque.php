<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $pendiente_cobro_id
 * @property string $banco
 * @property string $referencia
 * @property string $importe
 * @property string $no_cuenta
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property PendientesCobro $pendientesCobro
 */
class TransferenciasCheque extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['pendiente_cobro_id', 'banco', 'referencia', 'importe', 'no_cuenta', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pendientesCobro()
    {
        return $this->belongsTo('App\PendientesCobro', 'pendiente_cobro_id');
    }
}
