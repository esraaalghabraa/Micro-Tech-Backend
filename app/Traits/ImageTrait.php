<?php
namespace App\Traits;

use Illuminate\Support\Facades\File as FacadesFile;

Trait ImageTrait{


    private function setImage( $request,$fileName,$folderName)
    {
        $path = $request->file($fileName)->storeAs($folderName, uniqid() . '.' . $request->file($fileName)->extension(), 'images');
        $path = explode('/', $path);
        return $path[1];
    }

    private function setItemImage( $item,$folderName)
    {
        $path =$item->storeAs($folderName, uniqid() . '.' . $item->extension(), 'images');
        $path = explode('/', $path);
        return $path[1];
    }
    private function getImage( $image,$folderName)
    {
        return $image ? asset('assets/images/'.$folderName.'/'.$image) : asset('assets/images/im5.png');
    }

    private function getImagesArray($images, $folderName)
    {
        $hero_images=[];
        if ($images==null)
            return [];
        foreach ($images as $i=>$image) {
            $hero_images[$i]=$image ? asset('assets/images/'.$folderName.'/'.$image) : asset('assets/images/im5.png');
        }
        return $hero_images;
    }

    public function deleteImage($folderName,$image){
        FacadesFile::delete(public_path('assets\images\\'.$folderName .'\\' . $image));
    }



}

?>
