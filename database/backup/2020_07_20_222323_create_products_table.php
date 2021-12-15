<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('price')->nullable();
            $table->string('cover_img')->nullable();
            $table->string('compared_as')->nullable();
            $table->string('cpu')->nullable();
            $table->string('profit')->nullable();
            $table->string('profit_margin')->nullable();
            $table->string('qty_before')->nullable();
            $table->string('quantity')->nullable();
            $table->string('discount')->nullable();
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->boolean('track_qty')->default(true);
            $table->boolean('continue_selling')->default(true);
            $table->string('status')->nullable();
            $table->string('category')->nullable();
            $table->string('product_type')->nullable();
            $table->boolean('best_seller')->default(false);
            $table->boolean('new_arrival')->default(false);
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
        Schema::dropIfExists('products');
    }
}
