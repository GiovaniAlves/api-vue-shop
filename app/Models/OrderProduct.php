<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'quantity', 'price', 'order_id', 'product_id'
    ];

    protected $table = 'order_product';

    // Retorna apenas o(s) nome(s) do(s) produto(s) comprado(s)
    public function productName($product_id)
    {
        return Product::where('id', '=', $product_id)->select('name')->first();
    }
}
