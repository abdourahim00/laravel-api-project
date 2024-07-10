<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Cours extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'duration',
        'price',
        'category_id',
        'video',
        'featured'
    ];

    public function category()
{
    return $this->belongsTo(Category::class);
}
}
