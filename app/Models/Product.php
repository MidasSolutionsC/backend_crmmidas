<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model implements AuthorizableContract, AuthenticatableContract
{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    protected $table = "productos";

    protected $fillable = [
        'tipo_servicios_id',
        'categorias_id',
        'marcas_id',
        'tipo_producto',
        'nombre',
        'especificaciones',
        'descripcion',
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

    // En el modelo Promotion
    public function typeService()
    {
        return $this->belongsTo(TypeService::class, 'tipo_servicios_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categorias_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'marcas_id');
    }

    public function precios()
    {
        return $this->hasMany(ProductPrice::class, 'productos_id');
    }

    public function latestPrice()
    {
        return $this->hasOne(ProductPrice::class, 'productos_id')->latest();
    }

    public function getLastPrice()
    {
        $ultimoPrecio = $this->precios()->latest('created_at')->first();
        return $ultimoPrecio ? $ultimoPrecio->precio : null;
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class, 'productos_id');
    }
}
