<?php

use App\Models\category;

function getCategories(){
    return category::orderBy('name','ASC')
            ->with('sub_category')
            ->orderBy('id','DESC')
            ->where('status',1)
            ->where('showHome','Yes')
            ->get();
}
?>