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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id(); // شناسه یکتا برای هر نظر
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // شناسه محصول
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); ; // شناسه کاربر (در صورت لاگین بودن)
            $table->decimal('rating', 3, 2); // امتیاز از 0 تا 5 (مثلاً 4.5)
            $table->text('review')->nullable(); // متن نظر کاربر
            $table->boolean('is_approved')->default(false); // آیا نظر تایید شده یا نه (برای مدیریت نظرات)
            $table->timestamps(); // زمان ایجاد و به‌روزرسانی نظر
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};
