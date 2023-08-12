<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Address extends Model implements AuthenticatableContract, AuthorizableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "direcciones";

    protected $fillable = [
        'empresas_id',
        'personas_id',
        'domicilio',
        'tipo',
        'direccion',
        'numero',
        'escalera',
        'portal',
        'planta',
        'puerta',
        'codigo_postal',
        'localidad',
        'provincia',
        'territorial',
        'es_principal',
        'estado',
        'created_at',
        'updated_at',
        'deleted_at',  
    ];

    // public $timestamps = false;
}