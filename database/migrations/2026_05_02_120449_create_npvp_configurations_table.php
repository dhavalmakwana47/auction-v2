<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('npvp_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained('auctions')->cascadeOnDelete();
            $table->integer('starting_period');
            $table->integer('ending_period');
            $table->decimal('percentage_value', 15, 7);
            $table->integer('index');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('npvp_configurations');
    }
};
