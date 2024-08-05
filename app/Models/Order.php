<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Cours;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
       'user_id',
       'total_price',
       'status',
       'quantity'
    ];

    public function cours(){
        return $this->belongsTo(Cours::class);
    }
}
