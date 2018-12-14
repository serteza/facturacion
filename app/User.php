<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

/**
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string $password
 * @property string $rol
 * @property string $deleted_at
 * @property string $created_at
 * @property string $updated_at
 * @property AdtUser[] $adtUsers
 * @property UsersHasEmpresa[] $usersHasEmpresas
 */
class User extends Model implements
AuthenticatableContract,
AuthorizableContract
{
    use Authenticatable, Authorizable;
    /**
     * @var array
     */
    protected $fillable = ['email', 'name', 'password', 'rol', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adtUsers()
    {
        return $this->hasMany('App\AdtUser');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function usersHasEmpresas()
    {
        return $this->hasMany('App\UsersHasEmpresa');
    }
}
