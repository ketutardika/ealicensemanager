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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();            
            // Foreign key to link the license to a user
            $table->foreignId('user_id')->constrained('users');
            // Foreign key to link the license to an order
            $table->foreignId('order_id')->constrained('orders'); // Adding the order_id relationship
            $table->string('license_key')->unique();
            $table->integer('account_quota')->default(1);
            $table->integer('used_quota')->default(0);
            $table->dateTime('license_creation_date');
            $table->enum('license_expiration', ['1 month','3 months','6 months','1 year','2 years','3 years','lifetime'])->default('lifetime');
            $table->dateTime('license_expiration_date')->nullable();
            $table->enum('status', ['active', 'expired','inactive'])->default('active');
            $table->json('source')->nullable();
            $table->string('subscription_id')->nullable();
            $table->enum('subscription_status', ['active', 'expired', 'cancelled', 'on-hold', 'pending-cancellation'])->default('active');
            $table->dateTime('renewal_date')->nullable();
            $table->dateTime('last_renewal_date')->nullable();
            $table->enum('payment_status', ['paid', 'unpaid', 'failed'])->default('paid');
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
        Schema::dropIfExists('licenses');
    }
};
