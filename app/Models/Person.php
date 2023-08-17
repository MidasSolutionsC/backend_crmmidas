<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Person extends Model implements AuthenticatableContract, AuthorizableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "personas";

    protected $fillable = [
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'paises_id',
        'distritos_id',
        'tipo_documentos_id',
        'documento',
        'reverso_documento',
        'fecha_nacimiento',
        'telefono',
        'correo',
        'direccion',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $timestamps = false;
}