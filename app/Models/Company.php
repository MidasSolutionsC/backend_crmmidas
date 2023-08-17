<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Company extends Model implements AuthenticatableContract, AuthorizableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "empresas";

    protected $fillable = [
        'paises_id',
        'distritos_id',
        'razon_social',
        'nombre_comercial',
        'descripcion',
        'tipo_documentos_id',
        'documento',
        'tipo_empresa',
        'direccion',
        'ciudad',
        'telefono',
        'correo',
        'is_active ',
        'user_create_id ',
        'user_update_id ',
        'user_delete_id ',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $timestamps = false;
}