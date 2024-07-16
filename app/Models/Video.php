<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Cours;

class Video extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'video_file',
        'duration',
        'cours_id',
        'slug',
    ];

    public function getVideoFileAttribute() {
        return getenv('APP_URL')."/storage/".$this->attributes['video_file'];
    }

    public function cours() {
        return $this->belongsTo(Cours::class);
    }
}
