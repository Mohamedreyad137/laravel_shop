<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
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
        $rules = [
            'title' => 'required',
            'slug' => 'required',
            'price' => 'required|numeric',
            'sku' => 'required',
            'track_qty' => 'required|in:Yes,No',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];

        if (!empty($request->track_qty) && $request->track_qty == 'Yes'){
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->passes()){

        } else {
            return response()->json([
                'status' => false ,
                'errors' => $validator->errors()
            ]);
        }
    }
}
