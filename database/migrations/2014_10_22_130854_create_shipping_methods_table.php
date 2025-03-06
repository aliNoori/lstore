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
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // نام روش ارسال
            $table->text('description')->nullable(); // توضیحات اختیاری
            $table->decimal('cost', 8, 2); // هزینه ارسال
            $table->string('delivery_time')->nullable(); // زمان تخمینی تحویل
            $table->boolean('is_active')->default(true); // فعال یا غیرفعال بودن روش ارسال
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
        Schema::dropIfExists('shipping_methods');
    }
};
