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

    /**
     * categorias
     */
    public $categoryOptions = [
        'electronic' => 'Eletroeletrônico',
        'hardware' => 'Hardware',
        'home_appliance' => 'Eletrodoméstico'
    ];

    public function specifications()
    {
        return $this->belongsToMany(Specification::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
