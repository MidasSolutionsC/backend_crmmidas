<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements AuthorizableContract, AuthenticatableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "productos";

    protected $fillable = [
        'tipo_servicios_id',
        'nombre',
        'descripcion',
        'precio',
        'user_create_id',
        'user_update_id',
        'user_delete_id',
        'estado',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    

    // public $timestamps = false;
}