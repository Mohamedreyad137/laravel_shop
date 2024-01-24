<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;


class TempImagesController extends Controller
{
    public function create(Request $request){

        if($request->image){
            $image = $request->image;
            $ext = $image->getClientOriginalExtension();
            $newName = time().'.'.$ext;

            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path().'uploads/temp',$newName);

            //Generate thumbnail
            // $sourcePath = public_path().'/temp/'.$newName;
            // $destPath = public_path().'/temp/thumb/'.$newName;
            // $manager = new ImageManager(new Driver());
            // $image = $manager->read($sourcePath);
            // $image = $image->resize(300, 275);
            // // $image = Image::make($sourcePath);
            // // $image->fit(300,275);
            // $image->toPng()->save($destPath);

            return response()->json([
                'status' => true,
                // 'image_id' => $tempImage->id,
                // 'ImagePath' => asset('/temp/thumb/'.$newName),
                // 'message' => 'Image uploaded successfully'
                'name' => $newName,
                'id' => $tempImage->id
            ]);
        }
    }
}
