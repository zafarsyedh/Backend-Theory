<?php
namespace App\Traits;

use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

trait HandleFiles
{


    public function handleFiles( $file, $path ) : string
    {

    if ($file)  {
        $uniqueid = uniqid();
        $extension = $file->getClientOriginalExtension();
        $filename =$path.$uniqueid.'.'.$extension;
//        $file->move('storage/uploads/'.$path, $filename)
        $file->move(public_path('storage/uploads/'.$path), $filename);
        return  $filename;
    }


    }
}
?>
