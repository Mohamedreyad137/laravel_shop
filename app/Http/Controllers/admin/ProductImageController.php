<?php

namespace App\Http\Controllers\admin;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductImageController extends Controller
{
    public function update(Request $request){

        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName();

        $productImage = new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = 'Null';
        $productImage->save();
                   
        $imageName = $request->product_id.'-'.$productImage->id.'-'.time().'.'.$ext;
        $productImage->image = $imageName;
        $productImage->save();

        $destPath = public_path().'/uploads/product/large/'.$imageName;
        $manager = new ImageManager(new Driver());
        $image = $manager->read($sourcePath);
        $image = $image->resize(1400,null,function($constraint){
            $constraint->aspectRatio();
        });
        $image->toPng()->save($destPath);            


        //Small Image
        $destPath = public_path().'/uploads/product/small/'.$imageName;
        $manager = new ImageManager(new Driver());
        $image = $manager->read($sourcePath);
        $image = $image->cover(300,300);
        $image->toPng()->save($destPath);

        return response()->json([
            'status'=> true,
            'image'=> $productImage->id,
            'ImagePath'=> asset('/uploads/product/small/'.$productImage->image),
            'message'=> "Image Saved successfully",
        ]);
    }

    public function destroy(Request $request){
        $productImage = ProductImage::find($request->id);
        
        if(empty($productImage)) {
            return response()->json([
                'status'=> false,
                'message'=> "No Image Found"
            ]);
        }

        // Delete images from foldere
        File::delete(public_path('uploads/product/large/'.$productImage->image));
        File::delete(public_path('uploads/product/small/'.$productImage->image));

        $productImage->delete();

        return response()->json([
            'status'=>true,
            'message'=>"Image Deleted Successfully",
        ]);
        
    }
 }
