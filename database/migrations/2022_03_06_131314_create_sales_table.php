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
            $table->string('store_id');
            $table->string('receipt')->nullable();
            $table->string('total_paid');
            $table->boolean('discounted')->default(false);
            $table->string('discount_val')->nullable();
            $table->string('price_before')->nullable();
            $table->string('amount_recieved')->nullable();
            $table->string('balance')->nullable();
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
        Schema::dropIfExists('sales');
    }
}
