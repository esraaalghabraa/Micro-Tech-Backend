<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    use HasFactory,ImageTrait;
    protected $guarded=[];
    protected $hidden=['pivot','icon'];
    protected $table='tools';
    protected $appends=['icon_url'];
    public function getIconUrlAttribute()
    {
        return $this->getImage($this->icon,'tools');
    }
//    protected function Icon(){
//        return Attribute::make(
//            get:fn ($value) => $this->getImage($value,'tools')
//        );
//    }
    public function projects(){
        $this->belongsToMany(Project::class,ToolProject::class);
    }
}
