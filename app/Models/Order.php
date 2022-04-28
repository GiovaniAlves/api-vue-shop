<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    /**
     * categorias
     */
    public $categoryOptions = [
        'electronic' => 'Eletroeletrônico',
        'hardware' => 'Hardware',
        'home_appliance' => 'Eletrodoméstico'
    ];

    public function client()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product');
    }

    // Nessa função quero obter todas as informações, ex: qtd e preço, dos produtos na data da compra.
    public function orderProducts($order_id)
    {
        $orderProducts = OrderProduct::where('order_id', '=', $order_id)->get();

        return $orderProducts;
    }


    public function search($month, $year, $type) // type: 'status' ou 'category'
    {
        $orders = $this->whereMonth('orders.created_at', $month)->whereYear('orders.created_at', $year);

        if ($type === 'status') {
            $orders = $orders
                ->groupBy('status')
                ->select('status as label', DB::raw('SUM(total) as total'), DB::raw('COUNT(status) as quantity'))
                ->get();
        } else {
            $orders = $orders
                ->join('order_product', 'orders.id', '=', 'order_product.order_id')
                ->join('products', 'order_product.product_id', '=', 'products.id')
                ->groupBy('products.category')
                ->select('products.category as label', DB::raw('COUNT(products.category) as quantity'),  DB::raw('SUM(order_product. price) as total'))
                ->get();
        }

        return $orders;
    }
}
