<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('npvp_configurations', function (Blueprint $table) {
            $table->dropColumn(['starting_period', 'ending_period']);
            $table->integer('period')->after('auction_id');
        });
    }

    public function down(): void
    {
        Schema::table('npvp_configurations', function (Blueprint $table) {
            $table->dropColumn('period');
            $table->integer('starting_period');
            $table->integer('ending_period');
        });
    }
};
