<?php   
namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    const USERNAME_FIELD = 'nombre_usuario'; // Cambia este valor al campo de nombre de usuario
    const PASSWORD_FIELD = 'clave'; // Cambia este valor al campo de clave

    
    protected $table = "usuarios";

    protected $fillable = [
        'personas_id',
        'tipo_usuarios_id',
        'nombre_usuario',
        'clave',
        'session_activa',
        'foto_perfil',
        'is_active',
        'ultima_conexion',
        'api_token',
        'expires_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'clave',
        'api_token',
        'expires_at',
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
     * Retrieve the identifier for the JWT key.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    

    // Enlaces a tablas foráneas
    public function person(){
        return $this->belongsTo(Person::class, 'personas_id');
    }

    // Enlaces a tablas foráneas
    public function typeUser(){
        return $this->belongsTo(TypeUser::class, 'tipo_usuarios_id');
    }

    public function identifications()
    {
        return $this->hasMany(IdentificationDocument::class, 'personas_id')
            ->join('tipo_documentos as TD', 'documentos_identificaciones.tipo_documentos_id', '=', 'TD.id')
            ->select(
                'documentos_identificaciones.id', 
                'documentos_identificaciones.personas_id', 
                'documentos_identificaciones.tipo_documentos_id', 
                'documentos_identificaciones.documento', 
                'documentos_identificaciones.reverso_documento', 
                'documentos_identificaciones.is_primary', 
                'TD.nombre as tipo_documentos_nombre',
                'TD.abreviacion as tipo_documentos_abreviacion');
    }

    public function identificationDocumentWithType(){
        return $this->hasManyThrough(
            IdentificationDocument::class,
            Person::class,
            'personas_id',  // Nombre de la clave foránea en la tabla personas
            'personas_id', // Nombre de la clave foránea en la tabla identification_documents
            'id',           // Nombre de la clave primaria en la tabla usuarios
            'id'            // Nombre de la clave primaria en la tabla personas
        );
    }

}