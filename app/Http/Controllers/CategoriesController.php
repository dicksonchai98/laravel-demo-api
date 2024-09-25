<?php

namespace App\Http\Controllers;
use \App\Models\Category;

use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index(){
        return response(['data'=>Category::get()]);
    }

    public function categoryPosts($id)
{
    $category = Category::find($id);
    if(!is_null($category)){
        return response(['data' => $category->posts]);
    }
    return respoonse(['message' => 'Category not found!']);
}
}