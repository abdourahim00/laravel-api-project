<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cours;

class CoursController extends Controller
{
    public function create(Request $request){
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'required',
            'duration' => 'required',
            'price' => 'required|numeric',
        ]);
        $cours = Cours::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
            'image' => $request->image,
        ]);

        return response()->json([
            'message' => 'Cours created successfully',
            'cours' => $cours
        ]);

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
                return response()->json([
                    'message' => 'Price does not match'
                ], 400);
            }

            // Logic for buying the course can be placed here if needed

            return response()->json([
                'message' => 'Course bought successfully',
                'cours' => $cours
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Cours not found'
            ], 404);
        }
    }

    public function updateCours($id, Request $request) {
        $cours = Cours::findOrFail($id);
        $cours->update($request->all());
        return response()->json([
            'message' => 'Cours updated successfully',
            'cours' => $cours
        ]);
    }

    public function deleteCours($id) {
        $cours = Cours::findOrFail($id);
        $cours->delete();
        return response()->json([
            'message' => 'Cours deleted successfully'
        ]);
    }

}
