<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cours;
use Illuminate\Support\Str;


class CoursController extends Controller
{
    public function create(Request $request){
        $request->validate([
            'title' => 'required|string',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'duration' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            // 'video' => 'required|file|mimetypes:video/mp4',
            'featured' => 'in:true,false',
        ]);
        $filename = $request->file('image')->getClientOriginalName();
        // $fileVideo = $request->file('video')->getClientOriginalName();
        // $getName = getClientOriginalName();
        $cours = Cours::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            // 'duration' => $request->duration,
            'image'=>$request->file('image')->storeAs('images', $filename, 'public'),
            'category_id' => $request->category_id,
            // 'video' => $request->video,
            'featured' => $request->featured,
            'slug' => Str::slug($request->title, '-'),
        ]);

        return response()->success('Cours created successfully', ['cours' => $cours]);

    }

    public function buyCours($id, Request $request) {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'You must be logged in to buy a cours'
            ], 401);
        }

        try {
            $cours = Cours::findOrFail($id);

            if ($cours->price != $request->price) {
                // return response()->json([
                //     'message' => 'Price does not match'
                // ], 400);
                return response()->error('Price does not match');
            }

            // Logic for buying the course can be placed here if needed

            // return response()->json([
            //     'message' => 'Course bought successfully',
            //     'cours' => $cours
            // ]);
            return response()->success('Course bought successfully', ['cours' => $cours]);
        } catch (ModelNotFoundException $e) {
            // return response()->json([
            //     'message' => 'Cours not found'
            // ], 404);
            return response()->error('Cours not found');
        }
    }

    public function updateCours($slug, Request $request) {
        $cours = Cours::where('slug', $slug)->first();

        $validatedData = $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'duration' => 'nullable|string',
            'price' => 'nullable|numeric',
            'category_id' => 'nullable|exists:categories,id',
            // 'video' => 'required|file|mimetypes:video/mp4',
            'featured' => 'in:true,false',
        ]);

        $cours->fill($validatedData);
        if ($request->hasFile('image')) {
            $filename = $request->file('image')->getClientOriginalName();
            $cours->image = $request->file('image')->storeAs('images', $filename, 'public');
        }
        // if($request->hasFile('video')){
        //     $filename = $request->file('video')->getClientOriginalName();
        //     $cours->video = $request->file('video')->storeAs('cours_video', $filename);
        // }
        // Save the updated course
        $cours->save();
        $list = Cours::with('category')->get();
        return response()->success('Cours updated successfully', ['cours' => $list]);
    }


    // public function getCoursByCategory($id) {
    //     $cours = Cours::where('category_id', $id)->get();
    //     return response()->success('Liste des cours par catégorie', ['cours' => $cours]);
    // }

    // public function getCours($feat) {
    //     $cours = Cours::where('featured', $feat)->first();
    //     return response()->success('Liste des cours', ['cours' => $cours]);
    // }

    public function index(Request $request){

        $featured = $request->query('featured');
        // dd($featured);

        if($featured != null){
            if($featured === 'false' || $featured === 'true'){
                $cours = Cours::where('featured', $featured)->get();
                $list = Cours::with('category')->get();
                return response()->success('Liste des cours en vedette', ['cours' => $list]);
            }
            else{
                return response()->error('Featured does not exist');
            }
        }
        $cours = Cours::all();
        $list = Cours::with('category')->get();
        return response()->success('Liste de tous les cours ', ['cours' => $list]);
        // $cours = Cours::with('category')->get();

    }

    // public function getDetailsCours($id) {
    //     $cours = Cours::findOrFail($id);
    //     return response()->success('Détails du cours', ['cours' => $cours]);
    // }

    public function deleteCours($slug) {
        $cours = Cours::where('slug', $slug)->first();
        if (!$cours) {
            return response()->error('Cours not found');
        }
        $cours->delete();

        return response()->success('Cours deleted successfully', ['cours' => $cours]);
    }


    public function coursesRecommended($id) {
        $cours = Cours::where('category_id', $id)->get();
        return response()->success('Liste des cours recommandes', ['cours' => $cours]);
    }

    public function getDetails($slug) {
        $cours = Cours::where('slug', $slug)->first();
        return response()->success('Détails du cours', ['cours' => $cours]);
    }

}
