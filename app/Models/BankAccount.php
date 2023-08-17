<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model implements AuthenticatableContract, AuthorizableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "cuentas_bancarias";

    protected $fillable = [
        'clientes_id',
        'tipo_cuentas_bancarias_id',
        'cuenta',
        'fecha_apertura',
        'is_primary',
        'is_active',
        'user_create_id',
        'user_update_id',
        'user_delete_id',
        'created_at',   
        'updated_at',
        'deleted_at',
    ];

    public $timestamps = false;
}