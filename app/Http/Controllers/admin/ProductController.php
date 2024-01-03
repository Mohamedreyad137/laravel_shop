<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function create(){
        $data = [];
        $categories = category::orderBy('name','ASC')->get();
        $subcategories = SubCategory::orderBy('name','ASC')->get();
        $brands = Brand::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['subcategories'] = $subcategories;
        $data['brands'] = $brands;
        return view('admin.products.create',$data);
    }

    public function store(){

    }
}
