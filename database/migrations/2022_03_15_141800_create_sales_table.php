<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->float('total');
            $table->enum('status', ['ordered', 'paid', 'unpaid', 'delivered', 'canceled'])->default('ordered');
            $table->timestamps();

            /* TODO tem que testar pra ver se esse método impede a exclusão pois não excluir um venda  ->restrictOnDelete();*/
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('order_product', function (Blueprint $table) {
           $table->id();
           $table->integer('quantity');
           $table->float('price');
           $table->unsignedBigInteger('order_id');
           $table->unsignedBigInteger('product_id');

           $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
           $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_product');
        Schema::dropIfExists('sales');
    }
}
