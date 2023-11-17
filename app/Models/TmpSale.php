<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TmpSale extends Model implements AuthorizableContract, AuthenticatableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "tmp_ventas";
    
    protected $fillable = [
        'nro_orden',
        'retailx_id',
        'smart_id',
        'direccion_smart_id',
        'clientes_id',
        'fecha',
        'hora',
        'comentario',
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
    ];

    /**
     * Relación de pertenencia entre modulo externo
     */
    public function client(){
        return $this->belongsTo(Client::class, 'clientes_id');
    }

    public function saleDetails()
    {
        return $this->hasMany(TmpSaleDetail::class, 'ventas_id', 'id');
    }

}