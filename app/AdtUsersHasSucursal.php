<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $adt_user_id
 * @property int $sucursal_id
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property Sucursal $sucursal
 * @property AdtUser $adtUser
 */
class AdtUsersHasSucursal extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'adt_users_has_sucursales';

    /**
     * @var array
     */
    protected $fillable = ['adt_user_id', 'sucursal_id', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sucursal()
    {
        return $this->belongsTo('App\Sucursal', 'sucursal_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adtUser()
    {
        return $this->belongsTo('App\AdtUser');
    }
}
