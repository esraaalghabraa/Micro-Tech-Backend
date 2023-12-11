<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory,ImageTrait;
    protected $guarded=[];
    protected $hidden=['pivot','images'];
    protected $appends=['images_feature'];
    protected $casts=['images'=>'array'];


    protected function getImagesFeatureAttribute(){
        return  $this->getImagesArray($this->images,'feature_images');
    }

    public function project(){
        $this->belongsTo(Project::class,'project_id');
    }
}
