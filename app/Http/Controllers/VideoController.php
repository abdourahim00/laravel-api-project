<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Str;
use FFMpeg;
// use FFMpeg\FFMpeg;


class VideoController extends Controller
{
    public function create(Request $request)
    {
    $request->validate([
        'video_file' => 'required|file|mimetypes:video/mp4',
        'cours_id' => 'required|exists:cours,id',
    ]);

    $filename = $request->file('video_file')->getClientOriginalName();
    $name = pathinfo($filename, PATHINFO_FILENAME);
    $path = $request->video_file->storeAs('cours_video', $filename, 'public');

    $ffmpeg = FFMpeg\FFMpeg::create([
        'ffmpeg.binaries'  => env('FFMPEG_BINARIES', 'ffmpeg'),
        'ffprobe.binaries' => env('FFPROBE_BINARIES', 'ffprobe'),
        'timeout'          => 3600,
        'ffmpeg.threads'   => 1,
    ]);
    $video = $ffmpeg->open(storage_path($path, 'public'));
    $duration = $video->getFormat()->get('duration');

    $video = Video::create([
        'video_file' => $path,
        'title' => $name,
        'duration' => $duration,
        'cours_id' => $request->cours_id,
        'slug' => Str::slug($name, '-'),
    ]);

    $list = Video::with('cours')->get();
    return response()->success('Video created successfully', ['video' => $list]);
    }

    // private function convertDuration($duration) {
    //     $seconds = $duration;
    //     $hours = floor($seconds / 3600);
    //     $minutes = floor(($seconds % 3600) / 60);
    //     $seconds = $seconds % 60;
    //     $duration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    //     return $duration;
    // }

    public function index(){
        $list = Video::with('cours')->get();
        return response()->success('Liste des videos avec les cours correspondants', ['video' => $list]);
    }

    public function updateVideo(Request $request, $slug){
    $request->validate([
        'video_file' => 'nullable|file|mimetypes:video/mp4',
        'cours_id' => 'nullable|exists:cours,id',
    ]);

    $video = Video::where('slug', $slug)->firstOrFail();

    if ($request->hasFile('video_file')) {
        $filename = $request->file('video_file')->getClientOriginalName();
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $path = $request->video_file->storeAs('cours_video', $filename, 'public');

        // Récupérer la durée de la nouvelle vidéo
        $ffmpeg = FFMpeg\FFMpeg::create([
            'ffmpeg.binaries'  => env('FFMPEG_BINARIES', 'ffmpeg'),
            'ffprobe.binaries' => env('FFPROBE_BINARIES', 'ffprobe'),
            'timeout'          => 3600, // the timeout for the underlying process
            'ffmpeg.threads'   => 1,   // the number of threads that FFMpeg should use
        ]);
        $ffmpegVideo = $ffmpeg->open(storage_path('app/' . $path));
        $duration = $ffmpegVideo->getFormat()->get('duration');

        $video->video_file = $path;
        $video->title = $name;
        $video->duration = $duration;
        $video->slug = Str::slug($name, '-');
    }

    if ($request->has('cours_id')) {
        $video->cours_id = $request->cours_id;
    }

    $video->save();

    $list = Video::with('cours')->get();
    return response()->success('Video updated successfully', ['video' => $list]);
    }

    public function deleteVideo($slug){
        $video = Video::where('slug', $slug)->first();
        $video->delete();
        $list = Video::with('cours')->get();
        return response()->success('Video deleted successfully', ['video' => $list]);
    }

    public function getVideoByCours($coursID){
        $video = Video::where('cours_id', $coursID)->get();
        return response()->success('Liste des videos avec les cours correspondants', ['video' => $video]);
    }

}
