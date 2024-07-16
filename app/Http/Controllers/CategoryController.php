<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    public function create(Request $request){

        $request->validate([
            'name' => 'required',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
        ]);

        return response()->success('Category created successfully', ['category' => $category]);
    }


    public function update(Request $request, $slug){

        $request->validate([
            'name' => 'required',
        ]);

        $category = Category::where('slug', $slug)->first();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name, '-');
        $category->save();

        return response()->success('Category updated successfully', ['category' => $category]);
    }

    public function delete($slug){

        $category = Category::where('slug', $slug)->first();
        $category->delete();
        return response()->success('Category deleted successfully');
    }

    public function index(){
        $categories = Category::all();
        return response()->success('Categories retrieved successfully', ['categories' => $categories]);
    }

    
    public function getCountLessonsOfCategory($slug){
        $category = Category::where('slug', $slug)->first();
        $lecons = $category->lecons()->count();
        return response()->success('Lecons count retrieved successfully', ['lecons' => $lecons]);
    }

}
