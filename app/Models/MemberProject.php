<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberProject extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $table='member_projects';
    protected $hidden=['created_at','updated_at'];
}
