<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $tel
 * @property string $movil
 * @property string $mail
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Persona[] $personas
 */
class Contacto extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['tel', 'movil', 'mail', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function personas()
    {
        return $this->hasMany('App\Persona');
    }
}
