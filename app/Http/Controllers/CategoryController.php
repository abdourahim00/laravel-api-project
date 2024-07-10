<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function create(Request $request){

        $request->validate([
            'name' => 'required',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->save();

        return response()->success('Category created successfully', ['category' => $category]);
    }

    public function update(Request $request, $id){

        $request->validate([
            'name' => 'required',
        ]);

        $category = Category::find($id);
        $category->name = $request->name;
        $category->save();

        return response()->success('Category updated successfully', ['category' => $category]);
    }

    public function delete($id){
        $category = Category::find($id);
        $category->delete();
        return response()->success('Category deleted successfully');
    }

    //Afficher toutes les categories
    public function index(){
        $categories = Category::all();
        return response()->success('Categories retrieved successfully', ['categories' => $categories]);
    }

    //Nombre de cours dans une catégorie
    public function getCountCoursOfCategory($id){

        $category = Category::find($id);
        $cours = $category->cours;
        return response()->success('Nombre de cours dans une catégorie', count($cours));
    }

}
