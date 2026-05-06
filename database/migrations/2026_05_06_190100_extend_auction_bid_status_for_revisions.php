<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE auction_bids
            MODIFY COLUMN status ENUM('pending','confirmed','revision_pending','revision_rejected')
            NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // Normalize new statuses before shrinking enum values.
        DB::statement("
            UPDATE auction_bids
            SET status = 'pending'
            WHERE status IN ('revision_pending', 'revision_rejected')
        ");

        DB::statement("
            ALTER TABLE auction_bids
            MODIFY COLUMN status ENUM('pending','confirmed')
            NOT NULL DEFAULT 'pending'
        ");
    }
};

