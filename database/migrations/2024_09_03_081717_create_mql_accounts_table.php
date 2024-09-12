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
        Schema::create('mql_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained('licenses');
            $table->string('account_mql');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('validation_status', ['valid', 'invalid'])->default('valid');
            $table->timestamps();

            // Create a composite unique key on 'license_id' and 'account_mql'
            $table->unique(['license_id', 'account_mql'], 'license_account_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mql_accounts');
    }
};
