<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentGatewaysTable extends Migration
{
    public function up()
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('gateway', 100);
            $table->string('type', 100)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('terminal_id', 100)->unique();
            $table->text('wsdl')->nullable();
            $table->string('wsdl_confirm', 255)->nullable();
            $table->string('wsdl_reverse', 255)->nullable();
            $table->string('wsdl_multiplexed', 255)->nullable();
            $table->string('payment_gateway', 100);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_gateways');
    }
}
