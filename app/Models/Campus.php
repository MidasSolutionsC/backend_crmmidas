<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model implements AuthenticatableContract, AuthorizableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "sedes";

    protected $fillable = [
        'paises_id',
        'codigo_ubigeo',
        'nombre',
        'ciudad',
        'direccion',
        'codigo_postal',
        'telefono',
        'correo',
        'responsable',
        'fecha_apertura',
        'is_active',
        'logo',
        'user_create_id',
        'user_update_id',
        'user_delete_id',
        'created_at',
        'updated_at',
        'deleted_at',   
    ];

    // Evitar agregar fechas por defecto al crear y modificar
    public $timestamps = false;

    // Evitar ocultar campos al retornar el objeto
    protected $hidden = [];

    /**
     * TRANSFORMACIÓN DE VALORES
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    // Enlaces a la tabla países
    public function country(){
        return $this->belongsTo(Country::class, 'paises_id');
    }

    // Enlaces a la tabla países
    public function ubigeo(){
        return $this->belongsTo(Ubigeo::class, 'codigo_ubigeo', 'ubigeo');
    }

}