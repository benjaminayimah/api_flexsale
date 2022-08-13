<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->boolean('email_verified')->default(false);
            $table->string('password')->nullable();
            $table->enum('role', ['0', '1', '2' ])->default('1');
            $table->enum('subscription_type', ['0', '1', '2' ])->default('0');
            $table->boolean('oauth')->default(false);
            $table->string('oauth_id')->nullable();
            $table->string('oauth_provider')->nullable();
            $table->boolean('has_pass')->default(true);
            $table->string('current')->nullable();
            $table->string('admin_id')->nullable();
            $table->string('store_1')->nullable();
            $table->string('store_2')->nullable();
            $table->rememberToken()->nullable();
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
        Schema::dropIfExists('users');
    }
}
