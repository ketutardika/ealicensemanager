<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->string('order_id')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->integer('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('program_sn')->nullable();
            $table->decimal('total_purchase', 10, 2)->nullable();
            $table->string('currency')->nullable();
            $table->string('language')->nullable();
            $table->dateTime('transaction_date');
            $table->string('subscription_id')->nullable();
            $table->json('source')->nullable();
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
        Schema::dropIfExists('orders');
    }
};