<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolProject extends Model
{
    use HasFactory;
    protected $table='tools_projects';
    protected $guarded=[];
    protected $hidden=['created_at','updated_at'];

}
