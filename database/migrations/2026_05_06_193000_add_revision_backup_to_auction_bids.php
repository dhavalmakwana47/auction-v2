<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auction_bids', function (Blueprint $table) {
            if (!Schema::hasColumn('auction_bids', 'revision_backup')) {
                $table->longText('revision_backup')->nullable()->after('remark');
            }
        });
    }

    public function down(): void
    {
        Schema::table('auction_bids', function (Blueprint $table) {
            if (Schema::hasColumn('auction_bids', 'revision_backup')) {
                $table->dropColumn('revision_backup');
            }
        });
    }
};

