<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $serie
 * @property string $folio
 * @property string $cadena_original
 * @property string $xml
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property ComplementosPago[] $complementosPagos
 */
class TimbradosComplemento extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['serie', 'folio', 'cadena_original', 'xml', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function complementosPagos()
    {
        return $this->hasMany('App\ComplementosPago', 'timbrado_complemento_id');
    }
}
