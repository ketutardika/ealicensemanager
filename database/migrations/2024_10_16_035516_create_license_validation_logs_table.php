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
            $table->string('program_sn')->nullable();
            $table->string('account_mql')->nullable();
            $table->string('license_key')->nullable();
            $table->text('source')->nullable();
            $table->enum('validation_status', ['valid', 'invalid']);
            $table->text('message_validation')->nullable();  // Store the validation message
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->integer('account_quota')->nullable();
            $table->string('used_quota')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('license_validation_logs');
    }
}
