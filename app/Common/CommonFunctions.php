<?php

use App\Models\Category;
use Intervention\Image\ImageManager;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver;

const  sizes = [50,150,300,600,1200];
function saveImage(UploadedFile $file):string{

    $fileName = uniqid() . '.webp';
    $manager = new ImageManager(new Driver());
    foreach (sizes as $size) {
       $imageSave = $manager->read($file);
       $imageSave->scale(width: $size);
       $path = public_path(env('UPLOAD_DIR').$size."_".$fileName);
       $imageSave->toWebp()->save($path);
    }
    return $fileName;
}

function deteteImages(int $id){
    $item = Category::find($id);
    foreach (sizes as $size) {
        $filePath = public_path(env('UPLOAD_DIR').$size."_".$item->image);
        if(file_exists( $filePath)){
            unlink($filePath);
        }
    }
}
