<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //TODO:add is_default
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // اتصال به جدول کاربران
            $table->string('street'); // خیابان
            $table->string('city'); // شهر
            $table->string('state'); // ایالت یا استان
            $table->string('postal_code'); // کد پستی
            $table->string('country'); // کشور
            //TODO:add this field
            //$table->boolean('is_default')->default('false');
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
        Schema::dropIfExists('addresses');
    }
};
