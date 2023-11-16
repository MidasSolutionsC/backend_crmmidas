<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model implements AuthorizableContract, AuthenticatableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "ventas_detalles";

    protected $fillable = [
        'ventas_id',
        'productos_id',
        'promociones_id',
        'cantidad',
        'tipo_estados_id',
        'instalaciones_id',
        'fecha_cierre',
        'datos_json',
        'user_create_id',
        'user_update_id',
        'user_delete_id',
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
        'fecha_cierre' => 'datetime:Y-m-d H:i:s',
        'datos_json' => 'array',
    ];

    /**
     * Relación de pertenencia entre modulo externo
     */
    public function sale(){
        return $this->belongsTo(Sale::class, 'ventas_id');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'productos_id');
    }

    public function promotion(){
        return $this->belongsTo(Promotion::class, 'promociones_id');
    }

    public function installation(){
        return $this->belongsTo(Installation::class, 'instalaciones_id');
    }

    public function typeStatus(){
        return $this->belongsTo(TypeStatus::class, 'tipo_estados_id');
    }
}