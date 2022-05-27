<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trashes', function (Blueprint $table) {
            $table->id();
            $table->string('store_id');
            $table->string('name');
            $table->string('image')->nullable();
            $table->boolean('prod_type')->default(false);
            $table->string('cost')->nullable();
            $table->string('selling_price')->nullable();
            $table->string('stock')->nullable();
            $table->string('qty_before')->nullable();
            $table->text('description')->nullable();
            $table->string('supplier_id')->nullable();
            $table->string('discount')->nullable();
            $table->string('status')->nullable();
            $table->string('added_by');
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
        Schema::dropIfExists('trashes');
    }
}
