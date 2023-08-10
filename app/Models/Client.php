<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Client extends Model implements AuthenticatableContract, AuthorizableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;
     
    protected $table = "clientes";

    protected $fillable = [
        'tipo_documentos_id',
        'documento',
        'nombres',
        'paterno',
        'materno',
        'reverso_documento',
        'fecha_nacimiento',
        'nacionalidad',
        'domicilio_east1',
        'tipo',
        'direccion',
        'numero',
        'escalera',
        'portal',
        'planta',
        'puerta',
        'tipo_cliente',
        'persona_juridica',
        'razon_social',
        'cif',
        'codigo_postal',
        'localidad',
        'provincia',
        'codigo_carga',
        'territorial',
        'telefono_principal',
        'movil',
        'fijo',
        'correo',
        'cta_bco',
        'segmento_vodafond',
        'user_create_id ',
        'user_update_id ',
        'user_delete_id ',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // public $timestamps = false;
}