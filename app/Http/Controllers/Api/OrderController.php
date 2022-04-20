<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleFormRequest;
use App\Http\Resources\SaleResource;
use App\Models\Product;
use App\Models\Order;
use Cartalyst\Stripe\Stripe;
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
        $orders = $this->order->paginate(10);

        return SaleResource::collection($orders);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreSaleFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSaleFormRequest $request)
    {
        $data = $request->all();

        $data['client_id'] = auth()->user()->id;

        DB::beginTransaction();
        $order = $this->order->create($data);

        // Cadastrando os produtos comprados na tabela order_product e atualizando a quantidade dos produtos
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

        if ($allRight === true) {
            $order->update( ['status' => 'paid'] );
        } else {
            DB::rollBack();
            return response(['message' => 'Cartão recusado!'], 422);
        }

        DB::commit();
        return response(new SaleResource($order));
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

        return response(new SaleResource($order));
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
