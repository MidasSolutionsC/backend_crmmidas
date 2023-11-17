<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TmpSaleDetail extends Model implements AuthorizableContract, AuthenticatableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;
    
    protected $table = "tmp_ventas_detalles";
    
    protected $fillable = [
        'ventas_id',
        'productos_id',
        'promociones_id',
        'cantidad',
        'tipo_estados_id',
        'instalaciones_id',
        'observacion',
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
    public function tmpSale(){
        return $this->belongsTo(TmpSale::class, 'ventas_id');
    }

    public function tmpInstallation(){
        return $this->belongsTo(TmpInstallation::class, 'instalaciones_id');
    }

    public function product(){
        return $this->belongsTo(Product::class, 'productos_id');
    }

    public function promotion(){
        return $this->belongsTo(Promotion::class, 'promociones_id');
    }
}