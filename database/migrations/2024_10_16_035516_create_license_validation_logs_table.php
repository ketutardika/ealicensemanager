<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicenseValidationLogsTable extends Migration
{
    public function up()
    {
        Schema::create('license_validation_logs', function (Blueprint $table) {
            $table->id();
            $table->string('program_sn');
            $table->string('account_mql');
            $table->string('license_key');
            $table->text('source')->nullable();
            $table->enum('validation_status', ['valid', 'invalid']);
            $table->text('message_validation')->nullable();  // Store the validation message
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('account_quota')->nullable();
            $table->text('remaining_quota')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('license_validation_logs');
    }
}
