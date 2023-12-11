<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkTypesProject extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $table='work_types_projects';
    protected $hidden=['created_at','updated_at'];
}
