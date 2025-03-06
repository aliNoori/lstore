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
        Schema::create('views', function (Blueprint $table) {
            $table->id(); // شناسه یکتا
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // شناسه محصولی که بازدید شده است
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // شناسه کاربری که بازدید کرده (در صورت لاگین بودن)
            $table->ipAddress('ip_address')->nullable(); // آدرس IP بازدیدکننده (در صورت عدم ورود)
            $table->timestamps(); // زمان ایجاد و به‌روزرسانی بازدید
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('views');
    }
};
