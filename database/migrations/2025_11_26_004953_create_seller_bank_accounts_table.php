<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seller_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->cascadeOnDelete();
            $table->string('bank_name');
            $table->string('account_number'); 
            $table->string('account_holder'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_bank_accounts');
    }
};
