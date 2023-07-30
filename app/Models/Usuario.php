<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model implements AuthenticatableContract, AuthorizableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;
    protected $table = "usuarios";

    protected $fillable = [
        'grupos_id',
        'tipo_usuarios_id',
        'nombres',
        'paterno',
        'materno',
        'tipo_documentos_id',
        'documento',
        'correo',
        'clave',
        'fecha_nacimiento',
        'celular',
        'direccion',
        'foto',
        'logueado',
        'fotoestado',
        'estado',
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

    // public $timestamps = false;
}