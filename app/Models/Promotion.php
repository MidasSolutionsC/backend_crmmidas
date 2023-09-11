<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model implements AuthorizableContract, AuthenticatableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "promociones";

    protected $fillable = [
        'tipo_servicios_id',
        'nombre',
        'descripcion',
        'tipo_descuento',
        'descuento',
        'fecha_inicio',
        'fecha_fin',
        'codigo',
        'cantidad_minima',
        'cantidad_maxima',
        'user_create_id',
        'user_update_id',
        'user_delete_id',
        'is_private',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $timestamps = false;


    /**
     * TRANSFORMACIÓN DE VALORES
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    // Mutador para el campo fecha_fin
    public function setFechaFinAttribute($value) {
        // Verifica si el valor es una cadena vacía y establece null en su lugar
        $this->attributes['fecha_fin'] = empty($value) ? null : $value;
    }

    // En el modelo Promotion
    public function typeService()
    {
        return $this->belongsTo(TypeService::class, 'tipo_servicios_id');
    }

}