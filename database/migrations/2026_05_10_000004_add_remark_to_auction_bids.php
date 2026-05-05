<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auction_bids', function (Blueprint $table) {
            $table->text('remark')->nullable()->after('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('auction_bids', function (Blueprint $table) {
            $table->dropColumn('remark');
        });
    }
};
