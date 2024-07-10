<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cours;

class CoursController extends Controller
{
    public function create(Request $request){
        $request->validate([
            'title' => 'required|string',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'video' => 'required|max:50000|url',
            'featured' => 'nullable|boolean',
        ]);
        $filename = $request->file('image')->getClientOriginalName();
        // $fileVideo = $request->file('video')->getClientOriginalName();
        // $getName = getClientOriginalName();
        $cours = Cours::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
            'image'=>$request->file('image')->storeAS('cours_images', $filename),
            'category_id' => $request->category_id,
            'video' => $request->video,
            'featured' => $request->featured
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


    public function updateCours($id, Request $request) {
        $cours = Cours::findOrFail($id);

        $validatedData = $request->validate([
            'title' => 'nullable|string',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'nullable|string',
            'price' => 'nullable|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'video' => 'nullable|max:50000|url',
            'featured' => 'nullable|boolean',
        ]);

        $cours->fill($validatedData);

        if ($request->hasFile('image')) {
            $filename = $request->file('image')->getClientOriginalName();
            $cours->image = $request->file('image')->storeAs('cours_images', $filename);
        }
        // Save the updated course
        $cours->save();

        return response()->success('Cours updated successfully', ['cours' => $cours]);
    }


    public function getCoursByCategory($id) {
        $cours = Cours::where('category_id', $id)->get();
        return response()->success('Liste des cours par catégorie', ['cours' => $cours]);
    }

    public function getCours() {
        $cours = Cours::all();
        return response()->success('Liste des cours', ['cours' => $cours]);
    }

    public function getDetailsCours($id) {
        $cours = Cours::findOrFail($id);
        return response()->success('Détails du cours', ['cours' => $cours]);
    }

    public function deleteCours($id) {
        $cours = Cours::findOrFail($id);
        if ($cours) {
            $cours->delete();
            return response()->success('Cours deleted successfully');
        }
        return response()->error('Cours not found');
    }
}
