<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnologiesProject extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $table='technologies_projects';
    protected $hidden=['created_at','updated_at'];

}
