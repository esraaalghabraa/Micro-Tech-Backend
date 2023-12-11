<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkType extends Model
{
    use HasFactory,ImageTrait;
    protected $guarded=[];
    protected $hidden=['pivot','icon'];
    protected $appends=['icon_url'];
    public function getIconUrlAttribute()
    {
        return $this->getImage($this->icon,'work_types');
    }
    public function projects(){
        $this->belongsToMany(Project::class,WorkTypesProject::class);
    }
}
