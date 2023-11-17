<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Company extends Model implements AuthenticatableContract, AuthorizableContract{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "empresas";

    protected $fillable = [
        'paises_id',
        'codigo_ubigeo',
        'razon_social',
        'nombre_comercial',
        'descripcion',
        // 'tipo_documentos_id',
        // 'documento',
        'tipo_empresa',
        'is_active ',
        'user_create_id ',
        'user_update_id ',
        'user_delete_id ',
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
    

    public function client(){
        return $this->hasOne(Client::class, 'empresas_id', 'id');
    }

    public function identificationDocument()
    {
        return $this->hasMany(IdentificationDocument::class, 'empresas_id');
    }

    public function identifications()
    {
        return $this->hasMany(IdentificationDocument::class, 'empresas_id')
            ->join('tipo_documentos as TD', 'documentos_identificaciones.tipo_documentos_id', '=', 'TD.id')
            ->select(
                'documentos_identificaciones.id', 
                'documentos_identificaciones.empresas_id', 
                'documentos_identificaciones.tipo_documentos_id', 
                'documentos_identificaciones.documento', 
                'documentos_identificaciones.reverso_documento', 
                'documentos_identificaciones.is_primary', 
                'TD.nombre as tipo_documentos_nombre',
                'TD.abreviacion as tipo_documentos_abreviacion');
    }
    
    public function addresses()
    {
        return $this->hasMany(Address::class, 'empresas_id');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class, 'empresas_id');
    }
}