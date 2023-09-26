<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TmpSaleDocument extends Model implements AuthorizableContract, AuthenticatableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "tmp_ventas_documentos";

    protected $fillable = [
        'ventas_id',
        'ventas_detalles_id',
        'nombre',
        'tipo',
        'archivo',
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
    public function tmpSale(){
        return $this->belongsTo(TmpSale::class, 'ventas_id');
    }

    public function tmpSaleDetail(){
        return $this->belongsTo(TmpSaleDetail::class, 'ventas_detalles_id');
    }
}