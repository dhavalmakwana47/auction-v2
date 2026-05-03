<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auction_participants', function (Blueprint $table) {
            $table->boolean('sign_policy')->default(false)->after('user_id');
            $table->timestamp('sign_policy_at')->nullable()->after('sign_policy');
        });
    }

    public function down(): void
    {
        Schema::table('auction_participants', function (Blueprint $table) {
            $table->dropColumn(['sign_policy', 'sign_policy_at']);
        });
    }
};
