<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderFormRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ReportOrderCategoryResource;
use App\Http\Resources\ReportOrderStatusResource;
use App\Models\Product;
use App\Models\Order;
use Cartalyst\Stripe\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    private $order;
    private $product;

    public function __construct(Order $order, Product $product)
    {
        $this->order = $order;
        $this->product = $product;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $id = auth()->user()->id;
        $orders = $this->order->where('client_id', '=', $id)->get();

        return OrderResource::collection($orders);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreOrderFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderFormRequest $request)
    {
        $data = $request->all();

        $data['client_id'] = auth()->user()->id;

        DB::beginTransaction();
        $order = $this->order->create($data);

        // Cadastrando os produtos comprados na tabela order_product e atualizando a quantidade dos produtos na table products
        $products = $data['products'];
        $orderProducts = [];

        foreach ($products as $product) {
            $orderProducts[$product['id']] = [
                'quantity' => $product['quantity'],
                'price' => $product['price']
            ];

            $prod = $this->product->find($product['id']);
            if ($prod->quantity >= $product['quantity']) {
                $prod->update( ['quantity' => $prod->quantity - $product['quantity']] );
            }
        }

        $order->products()->attach($orderProducts);

        $data['exp_month'] = (int) substr($data['exp_date'], 0, 2);
        $data['exp_year'] = (int) substr($data['exp_date'], 3, 4);
        $data['order_id'] = $order->id;

        // Efetuando o pagamento
        $allRight = $this->stripPayment($data);
        // $allRight = true;

        if ($allRight === true) {
            $order->update( ['status' => 'paid'] );
        } else {
            DB::rollBack();
            return response(['message' => 'Cartão recusado!'], 422);
        }

        DB::commit();
        return response(new OrderResource($order));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!$order = $this->order->find($id)) {
            return response(['message' => 'Order not found!'], 404);
        }

        return response(new OrderResource($order));
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $data = $request->all();

        $data['month'] = substr($data['date'], 0, 2);
        $data['year'] = substr($data['date'], 3, 5);

        $orders = $this->order->search($data['month'], $data['year'], $data['type']);

        if ($data['type'] === 'status') {
            return response(ReportOrderStatusResource::collection($orders));
        } else {
            return response(ReportOrderCategoryResource::collection($orders));
        }
    }

    /**
     * @param array $data
     * @return bool|\Exception
     * @throws \Exception
     */
    public function stripPayment(array $data)
    {
        $stripe = Stripe::make(env('STRIPE_KEY'));

        try {

            $token = $stripe->tokens()->create([
                'card' => [
                    'number'    => $data['card_number'],
                    'exp_month' => $data['exp_month'],
                    'cvc'       => $data['cvc'],
                    'exp_year'  => $data['exp_year'],
                ]
            ]);

            if(!isset($token['id'])) {
                throw new \Exception('Invalid data');
            }

            $customer = $stripe->customers()->create([
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'phone' => '(33) 99999-9999',
                'address' => [
                    'line1' => 'Rua: Antônio Cezar, 7851',
                    'postal_code' => '39800-000',
                    'city' => 'Belo Horizonte',
                    'state' => 'Minas Gerais',
                    'country' => 'Brasil'
                ],
                'shipping' => [
                    'name' => auth()->user()->name,
                    'address' => [
                        'line1' => 'Rua: Antônio Cezar, 7851',
                        'postal_code' => '39800-000',
                        'city' => 'Belo Horizonte',
                        'state' => 'Minas Gerais',
                        'country' => 'Brasil'
                    ]
                ],
                'source' => $token['id']
            ]);

            $charge = $stripe->charges()->create([
                'customer' => $customer['id'],
                'currency' => 'BRL',
                'amount' => $data['total'],
                'description' => 'Pagamento do pedido de código ' . $data['order_id']
            ]);

            if ($charge['status'] === 'succeeded') {
                return true;
            }
            else {
                throw new \Exception('Error in payment');
            }

        } catch (\Exception $e) {
            return $e;
            // throw new \Exception('Error processing payment '. $e);
        }
    }
}
