<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'total', 'status'
    ];

    /**
     * Opções do status
     */
    public $statusOptions = [
        'ordered' => 'Pedido',
        'paid' => 'Pago',
        'unpaid' => 'Não Pago',
        'delivered' => 'Entregue',
        'canceled' => 'Cancelado'
    ];

    public function client()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product');
    }

    // Nessa função quero obter as quantidades e preços pagos nos produtos na data da compra.
    public function orderProducts($order_id)
    {
        $orderProducts = OrderProduct::where('order_id', '=', $order_id)->get();

        return $orderProducts;
    }
}
