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
        Schema::create('groceries', function (Blueprint $table) {
            $table->id();
            $table->string('ingredient_name');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('quantity');
            $table->timestamps();
            $table->unique(['ingredient_name','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groceries');
    }
};
