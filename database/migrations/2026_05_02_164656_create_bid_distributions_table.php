<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bid_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_bid_id')->constrained('auction_bids')->cascadeOnDelete();
            $table->foreignId('npv_category_id')->constrained('npv_categories')->cascadeOnDelete();
            $table->foreignId('npvp_configuration_id')->constrained('npvp_configurations')->cascadeOnDelete();
            $table->decimal('amount', 20, 2);
            $table->decimal('npv_value', 20, 7);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bid_distributions');
    }
};
