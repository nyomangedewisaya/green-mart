<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->string('order_code', 100)->unique();
            $table->integer('total_amount');
            $table->enum('status', ['pending', 'paid', 'shipped', 'completed', 'cancelled']);
            $table->string('payment_method')->default('transfer');
            $table->text('address');
            $table->string('shipping_courier')->nullable();
            $table->string('shipping_resi')->nullable();
            $table->date('order_date')->useCurrent();
            $table->date('receive_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
