<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{

    public function index(){

        $products = Product::latest('id')->with('product_images')->paginate();
        // dd($products);
        $data['products'] = $products;
        return view('admin.products.list',$data);
        // dd($products);
    }

    public function create(){
        $data = [];
        $categories = category::orderBy('name','ASC')->get();
        // $subcategories = SubCategory::orderBy('name','ASC')->get();
        $brands = Brand::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        // $data['subcategories'] = $subcategories;
        $data['brands'] = $brands;
        return view('admin.products.create',$data);
    }

    public function store(Request $request){

        // dd($request->image_array);
        // exit();
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes'){
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->passes()){

            $product = new Product;
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->save();

            //Save Gallery Pics
            if (!empty($request->image_array)) {
                foreach ($request->image_array as $key => $temp_image_id) {
                    
                    $tempImageInfo = TempImage::find($temp_image_id);
                    $extArray = explode('.',$tempImageInfo->name) ;
                    $ext = last($extArray); // like jpg,gif,png etc
                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image = 'Null';
                    $productImage->save();

                    $imageName = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage->image = $imageName;
                    $productImage->save();
                    // product_id=> 4 ; product_image_id=> 1
                    //4-1-10:15:00.jpg


                    //Generate Product Thumbnails


                    //Large Image
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
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
                }
            }

            $request->session()->flash('success','Product added successfully');

            return response()->json([
                'status' => true ,
                'message' => 'Product added successfully'
            ]);

        } else {
            return response()->json([
                'status' => false ,
                'errors' => $validator->errors()
            ]);
        }
    }
}
