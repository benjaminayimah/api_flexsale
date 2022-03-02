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
            $table->string('store_id');
            $table->string('name');
            $table->string('image')->nullable();
            $table->boolean('prod_type')->default(false);
            $table->string('cost')->nullable();
            $table->string('selling_price')->nullable();
            //$table->string('profit')->nullable();
            //$table->string('profit_margin')->nullable();
            $table->string('stock')->nullable();
            $table->string('qty_before')->nullable();
            //$table->boolean('track_qty')->default(true);
            $table->text('description')->nullable();
            $table->string('supplier')->nullable();
            $table->string('discount')->nullable();
            $table->string('status')->nullable();
            $table->string('added_by');


            // $table->boolean('best_seller')->default(false);
            // $table->boolean('new_arrival')->default(false);
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
