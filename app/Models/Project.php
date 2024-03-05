<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory,ImageTrait;
    protected $guarded=[];
    protected $hidden=['cover','logo','created_at','updated_at','active','special'];
    protected $appends=['cover_url','logo_url'];
    protected $casts=[
        'advantages'=>'array',
        'links'=>'array',
    ];
    protected function getCoverUrlAttribute(){
        return  $this->getImage($this->cover,'covers');
    }
    protected function getLogoUrlAttribute(){
        return $this->getImage($this->logo,'logos');
    }

    //relations
    public function features(){
        return $this->hasMany(Feature::class);
    }
    public function images(){
        return $this->hasMany(Image::class);
    }
    public function technologies(){
        return $this->belongsToMany(Technology::class,TechnologiesProject::class);
    }
    public function tools(){
        return $this->belongsToMany(Tool::class,ToolProject::class);
    }
    public function workTypes(){
        return $this->belongsToMany(WorkType::class,WorkTypesProject::class);
    }
    public function platforms(){
        return $this->belongsToMany(Platform::class,PlatformProject::class);
    }
    public function members(){
        return $this->belongsToMany(Member::class,MemberProject::class);
    }

    public function usersAddLike(){
        return $this -> belongsToMany(User::class,'likes');
    }

    public function likes(){
        return $this->hasMany(Like::class);
    }
    public function usersAddComment(){
        return $this -> belongsToMany(User::class,'comments');
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }
}
