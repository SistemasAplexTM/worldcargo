<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    public $table = "clientes";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'localizacion_id',
        'nombre',
        'direccion',
        'telefono',
        'email',
        'zona'
    ];
}
