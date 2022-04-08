<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'name', 'description', 'price', 'url', 'quantity', 'category', 'image'
    ];

    public function specifications()
    {
        return $this->belongsToMany(Specification::class);
    }
}
