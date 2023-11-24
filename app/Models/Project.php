<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function developers(){
        $this->belongsToMany(Developer::class,DevelopersProject::class);
    }
    public function platforms(){
        $this->belongsToMany(Platform::class,PlatformProject::class);
    }
    public function tools(){
        $this->belongsToMany(Tool::class,ToolProject::class);
    }
    public function types(){
        $this->belongsToMany(Type::class,TypeProject::class);
    }
    public function features(){
        return $this->hasMany(Feature::class);
    }
}
