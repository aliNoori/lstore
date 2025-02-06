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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to User
            $table->string('code', 50)->unique(); // Coupon code
            $table->dateTime('expire_date'); // Expiration date
            $table->decimal('discount_amount', 10, 2)->default(0.00); // Discount amount
            $table->enum('discount_type', ['percentage', 'fixed'])->default('fixed'); // Type of discount
            $table->boolean('is_used')->default(false); // Usage status
            $table->text('description')->nullable(); // Additional description
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
};
