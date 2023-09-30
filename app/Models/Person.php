<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Person extends Model implements AuthenticatableContract, AuthorizableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "personas";

    protected $fillable = [
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'paises_id',
        'codigo_ubigeo',
        'tipo_documentos_id',
        'documento',
        'reverso_documento',
        'fecha_nacimiento',
        // 'telefono',
        // 'correo',
        // 'direccion',
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

    public function country(){
        return $this->belongsTo(Country::class, 'paises_id');
    }

    public function ubigeo(){
        return $this->belongsTo(Ubigeo::class, 'codigo_ubigeo');
    }

    public function typeDocument(){
        return $this->belongsTo(TypeDocument::class, 'tipo_documentos_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class, 'personas_id');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'personas_id');
    }

}