<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SaleHistory extends Model implements AuthorizableContract, AuthenticatableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "ventas_historial";

    protected $fillable = [
        'ventas_id',
        'comentario',
        'tipo',
        'fecha',
        'hora',
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