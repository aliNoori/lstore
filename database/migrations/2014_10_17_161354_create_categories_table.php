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
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // شناسه یکتا برای هر دسته‌بندی
            $table->string('name'); // نام دسته‌بندی
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade'); // شناسه والد (در صورت وجود)
            $table->timestamps(); // زمان ایجاد و به‌روزرسانی دسته‌بندی
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
