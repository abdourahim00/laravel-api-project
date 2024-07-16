<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Video;

class Cours extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        // 'duration',
        'price',
        'category_id',
        // 'video',
        'featured',
        'slug'
    ];

    public function getImageAttribute(){
        return getenv('APP_URL')."/storage/".$this->attributes['image'];
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function video(){
        return $this->hasMany(Video::class);
    }

    public function leçons(){
        return $this->hasMany(Leçon::class);
    }
}
