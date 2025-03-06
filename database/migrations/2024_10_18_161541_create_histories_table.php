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
        Schema::create('histories', function (Blueprint $table) {
            $table->id(); // شناسه یکتا
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // رابطه با جدول محصولات
            $table->decimal('price_history', 10, 2); // قیمت تاریخی با دو رقم اعشار
            $table->dateTime('changed_at')->default(now()); // زمان تغییر قیمت
            $table->timestamps(); // زمان ایجاد و به‌روزرسانی تاریخچه
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('histories');
    }
};
