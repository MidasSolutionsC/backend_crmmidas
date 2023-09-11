<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Service extends Model implements AuthorizableContract, AuthenticatableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "servicios";

    protected $fillable = [
        'tipo_servicios_id',
        'productos_id',
        // 'instalaciones_id',
        // 'fecha_cierre',
        // 'datos_json',
        'promociones_id',
        'nombre',
        'descripcion',
        'tipo_estados_id',
        'is_active',
        'user_create_id',
        'user_update_id',
        'user_delete_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        // 'datos_json' => 'json',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public $timestamps = false;

}