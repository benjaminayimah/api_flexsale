<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->string('sale_id');
            $table->string('store_id');
            $table->string('product_id');
            $table->string('product_name');
            $table->string('quantity');
            $table->string('batch');
            $table->string('total_paid');
            $table->boolean('discounted')->default(false);
            $table->string('discount_val')->nullable();
            $table->string('price_before')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sale_items');
    }
}
