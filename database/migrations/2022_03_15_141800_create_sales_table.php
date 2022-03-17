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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('sale_product', function (Blueprint $table) {
           $table->id();
           $table->integer('quantity');
           $table->enum('status', ['ordered', 'paid', 'unpaid', 'delivered'])->default('ordered');
           $table->unsignedBigInteger('sale_id');
           $table->unsignedBigInteger('product_id');

           $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
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
