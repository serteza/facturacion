<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $persona_id
 * @property int $cliente_id
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Cliente $cliente
 * @property Persona $persona
 */
class PersonasHasCliente extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['persona_id', 'cliente_id', 'deleted_at', 'created_at', 'updated_at'];

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
    public function persona()
    {
        return $this->belongsTo('App\Persona');
    }
}
