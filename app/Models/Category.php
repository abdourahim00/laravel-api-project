<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Cours;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function cours()
{
    return $this->hasMany(Cours::class);
}
}
