<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory,ImageTrait;
    protected $guarded=[];
    protected $hidden=['pivot'];
    protected $casts=['images'=>'array'];



    public function project(){
        $this->belongsTo(Project::class,'project_id');
    }

    public function images(){
        return $this->hasMany(Image::class);
    }
}
