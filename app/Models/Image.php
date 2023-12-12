<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory, ImageTrait;

    protected $guarded = [];
    protected $hidden=['image','project_id','feature_id'];
    protected $appends=['image_url'];
    public function project()
    {
        $this->belongsTo(Project::class, 'project_id');
    }
    public function feature()
    {
        $this->belongsTo(Feature::class, 'feature_id');
    }
    protected function getImageUrlAttribute(){
        return  $this->getImage($this->image,'images');
    }
}
