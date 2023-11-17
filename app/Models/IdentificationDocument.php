<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IdentificationDocument extends Model  implements AuthenticatableContract, AuthorizableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "documentos_identificaciones";

    protected $fillable = [
        'personas_id',
        'empresas_id',
        'tipo_documentos_id',
        'documento',
        'reverso_documento',
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

        /**
     * TRANSFORMACIÓN DE VALORES
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function person(){
        return $this->belongsTo(Person::class, 'personas_id');
    }

    public function company(){
        return $this->belongsTo(Company::class, 'empresas_id');
    }
    
    public function typeDocument(){
        return $this->belongsTo(TypeDocument::class, 'tipo_documentos_id');
    }

}