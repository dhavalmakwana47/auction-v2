<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE auctions MODIFY COLUMN status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE auctions MODIFY COLUMN status ENUM('pending', 'in_progress', 'started') DEFAULT 'pending'");
    }
};
