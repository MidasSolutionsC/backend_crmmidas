<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SaleDocument extends Model implements AuthorizableContract, AuthenticatableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "ventas_documentos";

    protected $fillable = [
        'ventas_id',
        'ventas_detalles_id',
        'tipo_documentos_id',
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
    public function sale(){
        return $this->belongsTo(Sale::class, 'ventas_id');
    }

    public function saleDetail(){
        return $this->belongsTo(SaleDetail::class, 'ventas_detalles_id');
    }

    public function typeDocument(){
        return $this->belongsTo(TypeDocument::class, 'tipo_documentos_id');
    }

}