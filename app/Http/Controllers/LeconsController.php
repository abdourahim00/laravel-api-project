<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leçon;
use App\Models\Cours;

class LeconsController extends Controller
{
    public function create(Request $request){
        $request->validate([
            'title' => 'required|string',
            'description' => 'required',
            'cours_id' => 'required|exists:cours,id',
        ]);

        $lesson = Leçon::create([
            'title' => $request->title,
            'description' => $request->description,
            'cours_id' => $request->cours_id,
        ]);

        $lesson->save();

        return response()->success('Lecon created successfully', ['lesson' => $lesson]);
    }

    public function update(Request $request, $id){
        $lesson = Leçon::findOrFail($id);
        if(!$lesson){
            return response()->error('Lecon not found');
        }

        $request->validate([
            'title' => 'required|string',
            'description' => 'required',
            'cours_id' => 'required|exists:cours,id',
        ]);

        $lesson->title = $request->title;
        $lesson->description = $request->description;
        $lesson->cours_id = $request->cours_id;
        $lesson->save();
        return response()->success('Lecon updated successfully', ['lesson' => $lesson]);
    }

    public function delete($id){
        $lesson = Leçon::findOrFail($id);
        if(!$lesson){
            return response()->error('Lecon not found');
        }
        $lesson->delete();
        return response()->success('Lecon deleted successfully');
    }

    public function getCountLeconsOfCours($slug){
        $cours = Cours::findOrFail($slug);
        $lecons = $cours->lecons()->count();
        return response()->success('Lecons count retrieved successfully', ['lecons' => $lecons]);
    }

    public function index(){
        $list = Leçon::with('cours')->get();
        return response()->success('Liste des lecons avec les cours correspondants', ['lecons' => $list]);
    }
}
