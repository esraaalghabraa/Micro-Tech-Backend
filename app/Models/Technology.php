<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technology extends Model
{
    use HasFactory,ImageTrait;
    protected $guarded=[];
    protected $table='technologies';
    protected $hidden=['pivot','icon'];
    protected $appends=['icon_url'];
    public function getIconUrlAttribute()
    {
        return $this->getImage($this->icon,'Technologies');
    }
    public function projects(){
        $this->belongsToMany(Project::class,TechnologiesProject::class);
    }
}
