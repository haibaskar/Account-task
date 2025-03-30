<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('account_name');
            $table->string('password');
            $table->unsignedBigInteger('account_number')->unique();
            $table->enum('account_type', ['Personal', 'Business']);
            $table->enum('currency', ['USD', 'EUR', 'GBP', 'INR', 'JPY'])->default('USD');
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->rememberToken();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }




    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
