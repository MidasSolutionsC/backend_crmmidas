<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements AuthenticatableContract, AuthorizableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;
    protected $table = "usuarios";

    protected $fillable = [
        'personas_id',
        'tipo_usuarios_id',
        'nombre_usuario',
        'clave',
        'session_activa',
        'foto_perfil',
        'is_active',
        'ultima_conexion',
        'api_token',
        'expires_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'clave',
        'api_token',
        'expires_at',
    ];

    public $timestamps = false;
}