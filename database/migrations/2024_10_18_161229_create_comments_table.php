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
        Schema::create('comments', function (Blueprint $table) {
            $table->id(); // شناسه یکتا
            $table->morphs('commentable'); // ارتباط چند شکلی برای مدل‌های مختلف
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // شناسه کاربری که کامنت را ثبت کرده
            $table->text('body'); // متن کامنت
            $table->boolean('is_approved')->default(false); // وضعیت تأیید شدن کامنت
            $table->timestamps(); // زمان ایجاد و به‌روزرسانی کامنت
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
