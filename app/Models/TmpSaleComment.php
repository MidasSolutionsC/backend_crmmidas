<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TmpSaleComment extends Model implements AuthorizableContract, AuthenticatableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;
    
    protected $table = "tmp_ventas_comentarios";

    protected $fillable = [
        'ventas_id',
        'ventas_detalles_id',
        'comentario',
        'fecha',
        'hora',
        'is_active',
        'user_create_id',
        'user_update_id',
        'user_delete_id',
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
}