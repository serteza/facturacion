<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $password
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property User $user
 * @property AdtUsersHasSucursale[] $adtUsersHasSucursales
 */
class AdtUser extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'name', 'password', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adtUsersHasSucursales()
    {
        return $this->hasMany('App\AdtUsersHasSucursal');
    }
}
